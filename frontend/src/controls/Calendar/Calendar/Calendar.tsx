/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

import React, { ReactElement } from "react";
import "./Calendar.scss";
import moment from "moment";
import {
   Calendar as BigCalendar,
   momentLocalizer,
   NavigateAction,
   stringOrDate,
   View,
} from "react-big-calendar";
import withDragAndDrop from "react-big-calendar/lib/addons/dragAndDrop";
import { calendarMessages, getMonthHeader, getWeekHeader } from "./utils";
import { CalendarEvenement, Evenement } from "../../../lib/Evenement";
import CalendarEvent from "./CalendarEvent";
import Spinner from "../../Spinner/Spinner";
import {
   calculateRange,
   canCreateEventOnDate,
   createDateFromStringAsUTC,
   stringOrDateToDate,
   stringOrDateToString,
} from "../../../utils/dates";
import { useDispatch, useSelector } from "react-redux";
import { setModalEvenement, setModalEvenementId } from "../../../redux/actions/Modals";
import { IAccessibilite } from "../../../redux/context/IAccessibilite";
import { IStore } from "../../../redux/Store";
import { DensiteValues, IAffichageFiltres } from "../../../redux/context/IAffichageFiltres";
import { setAffichage, setFiltres } from "../../../redux/actions/AffichageFiltre";
import { useApi } from "../../../context/api/ApiProvider";
import { useAuth } from "../../../auth/AuthProvider";
import {
   PREFETCH_LAST_PERIODES_RH,
   PREFETCH_TYPES_EVENEMENTS,
} from "../../../api/ApiPrefetchHelpers";
import { App } from "antd";
import { ITypeEvenement } from "../../../api/ApiTypeHelpers";

const BigCalendarDnD = withDragAndDrop(BigCalendar);

interface ICalendar {
   events: Evenement[];
   setEvent: (event: Evenement) => void;
}

/**
 * Renders a calendar component with drag and drop functionality for creating and modifying events.
 *
 * @param {ICalendar} props - The component props.
 * @param {Evenement[]} props.events - The array of events to be displayed on the calendar.
 * @param {Function} props.setEvent - The function to set the selected event.
 *
 * @returns {ReactElement} - The rendered calendar component.
 */
export default function Calendar({ events, setEvent }: ICalendar): ReactElement {
   const { message } = App.useApp();
   const user = useAuth().user;
   const localizer = momentLocalizer(moment);
   const dispatch = useDispatch();
   const appAccessibilite: IAccessibilite = useSelector(
      ({ accessibilite }: IStore) => accessibilite,
   );
   const appAffichageFiltres: IAffichageFiltres = useSelector(
      ({ affichageFiltres }: IStore) => affichageFiltres,
   );
   const { data: typesEvenements, isFetching: isFetchingTypesEvenements } =
      useApi().useGetCollection(PREFETCH_TYPES_EVENEMENTS);

   // Dernière période dont la date butoir est dépassée
   const { data: lastPeriodes } = useApi().useGetCollection(PREFETCH_LAST_PERIODES_RH(user));

   // region "Calendar event handlers"

   /**
    * Handles the change event for a calendar event. (Déplacer un évènement en DnD)
    * @param {CalendarEvenement} event - The calendar event to be modified.
    * @param {stringOrDate} start - The new start date or string representation of a date for the event.
    * @param {stringOrDate} end - The new end date or string representation of a date for the event.
    * @returns {void}
    */
   const handleEventChange = (
      event: CalendarEvenement,
      start: stringOrDate,
      end: stringOrDate,
   ): void => {
      if (
         !canCreateEventOnDate(stringOrDateToDate(start), user, lastPeriodes?.items[0]) ||
         !canCreateEventOnDate(stringOrDateToDate(event.data.debut), user, lastPeriodes?.items[0])
      ) {
         message
            .error(
               "Seuls les administrateurs sont autorisés à modifier des évènements dans les périodes de saisie antérieures.",
            )
            .then();
         return;
      }

      if (event.data.dateEnvoiRH) {
         message.error("L'évènement n'est pas modifiable car il a déjà été envoyé à la RH.").then();
         return;
      }

      setEvent(
         new Evenement({
            ...event.data,
            debut: createDateFromStringAsUTC(stringOrDateToString(start)).toISOString(),
            fin: createDateFromStringAsUTC(stringOrDateToString(end)).toISOString(),
         }),
      );
   };

   /**
    * Handles the creation of an event. (Créer un évènement en DnD)
    *
    * @param {string | Date} start - The start date/time of the event.
    * @param {string | Date} end - The end date/time of the event.
    *
    * @returns {void}
    */
   const handleEventCreate = (start: stringOrDate, end: stringOrDate): void => {
      if (!canCreateEventOnDate(stringOrDateToDate(start), user, lastPeriodes?.items[0])) {
         message
            .error(
               "Seuls les administrateurs sont autorisés à créer des évènements dans les périodes de saisie antérieures.",
            )
            .then();
         return;
      }

      dispatch(
         setModalEvenement({
            debut: stringOrDateToString(start),
            fin: stringOrDateToString(end),
         }),
      );
   };

   function handleNavigation(date: Date, view: View, navigateAction: NavigateAction) {
      const range = calculateRange(
         date,
         navigateAction === "DATE" ? "day" : appAffichageFiltres.affichage.type,
      );
      dispatch(setFiltres({ debut: range.from, fin: range.to }));
   }

   function handleChangeView(view: View) {
      dispatch(setAffichage({ type: view }));
   }

   // endregion

   /**
    * Calculates the props object for displaying a calendar event.
    *
    * @param {CalendarEvenement} event - The calendar event object.
    * @returns {Object} - The props object containing the className and style properties.
    */
   const eventPropGetter = (event: CalendarEvenement): object => {
      const typeEvenement = typesEvenements?.items.find(
         (t: ITypeEvenement) => t["@id"] === event.data.type,
      );

      let props = {
         className: `border-radius ${event.data.dateAnnulation ? "event-annule" : ""}`,
         style: {
            fontSize:
               appAffichageFiltres.affichage.densite === DensiteValues.compact ? "0.8rem" : "1rem",
            backgroundColor: event.data.isAffecte()
               ? `var(--color-${typeEvenement?.couleur})`
               : `var(--color-xlight-${typeEvenement?.couleur})`,
            color: `var(--color-dark-${typeEvenement?.couleur})`,
            border: "none",
            backgroundImage: event.data.isAffecte() ? "" : "url(/images/strip.svg)",
         },
      };

      if (appAccessibilite.contrast) {
         props = {
            ...props,
            style: {
               ...props.style,
               backgroundColor: event.data.isAffecte()
                  ? `var(--color-dark-${typeEvenement?.couleur})`
                  : `var(--color-xlight-${typeEvenement?.couleur})`,
               color: event.data.isAffecte() ? `#FFF` : "#000",
               border: event.data.isAffecte() ? "none" : `5px solid var(--color-danger)`,
               backgroundImage: "",
            },
         };
      }

      return props;
   };

   function handleOpenModal(data: Evenement) {
      dispatch(setModalEvenementId(data["@id"]));
   }

   // Custom n'est valable que pour le layout Table
   if (appAffichageFiltres.affichage.type === "custom") {
      dispatch(setAffichage({ type: "work_week" }));
      return (
         <>
            <Spinner />
         </>
      );
   }

   if (isFetchingTypesEvenements)
      return (
         <div data-testid="wait">
            <Spinner />
         </div>
      );

   return (
      <>
         <BigCalendarDnD
            className={`calendar density-${appAffichageFiltres.affichage.densite?.toLowerCase()} calendar-view-${
               appAffichageFiltres.affichage.type
            } ${appAffichageFiltres.affichage.fitToScreen ? "fit-to-screen" : ""}`}
            localizer={localizer}
            timeslots={2}
            step={15}
            min={new Date(0, 0, 0, 7, 0, 0)}
            max={new Date(0, 0, 0, 22, 0, 0)}
            messages={calendarMessages}
            events={events.map((event) => {
               return event.toCalendarEvent();
            })}
            dayLayoutAlgorithm="no-overlap"
            view={appAffichageFiltres.affichage.type}
            views={["month", "week", "work_week", "day"]}
            onView={handleChangeView}
            date={appAffichageFiltres.filtres.debut}
            onNavigate={handleNavigation}
            onSelectEvent={(event) => {
               handleOpenModal((event as CalendarEvenement).data);
            }}
            eventPropGetter={(event) => eventPropGetter(event as CalendarEvenement)}
            selectable={user?.isPlanificateur}
            draggableAccessor={() => user?.isPlanificateur || false}
            toolbar={false}
            components={{
               toolbar: undefined,
               event: (event) => (
                  <CalendarEvent
                     key={(event.event as CalendarEvenement).data.hashCode()}
                     event={event.event as CalendarEvenement}
                  />
               ),
               month: {
                  header: ({ date }) => getMonthHeader(date),
               },
               work_week: {
                  header: ({ date }) =>
                     getWeekHeader(date, !canCreateEventOnDate(date, user, lastPeriodes?.items[0])),
               },
               week: {
                  header: ({ date }) =>
                     getWeekHeader(date, !canCreateEventOnDate(date, user, lastPeriodes?.items[0])),
               },
               day: {
                  header: ({ date }) => <>{date.toLocaleDateString()}</>,
               },
            }}
            onEventDrop={({ event, start, end }) => {
               if (user?.isPlanificateur) handleEventChange(event as CalendarEvenement, start, end);
            }}
            onEventResize={({ event, start, end }) => {
               if (user?.isPlanificateur) handleEventChange(event as CalendarEvenement, start, end);
            }}
            onSelectSlot={(slotInfo) => {
               if (user?.isPlanificateur) handleEventCreate(slotInfo.start, slotInfo.end);
            }}
         />
      </>
   );
}
