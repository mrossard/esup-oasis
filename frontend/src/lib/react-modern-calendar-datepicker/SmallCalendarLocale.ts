/*
 * Copyright (c) 2024. Esup - Université de Bordeaux
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 * For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 * @author Julien Lemonnier <julien.lemonnier@u-bordeaux.fr>
 */

// noinspection JSUnusedGlobalSymbols

export const modernCalendarLocaleFr = {
   // months list by order
   months: [
      "Janvier",
      "Février",
      "Mars",
      "Avril",
      "Mai",
      "Juin",
      "Juillet",
      "Août",
      "Septembre",
      "Octobre",
      "Novembre",
      "Décembre",
   ],

   // week days by order
   weekDays: [
      {
         name: "Lundi",
         short: "Lu",
      },
      {
         name: "Mardi",
         short: "Ma",
      },
      {
         name: "Mercredi",
         short: "Me",
      },
      {
         name: "Jeudi",
         short: "Je",
      },
      {
         name: "Venredi",
         short: "Ve",
      },
      {
         name: "Samedi",
         short: "Sa",
         isWeekend: true,
      },
      {
         name: "Dimanche", // used for accessibility
         short: "Di", // displayed at the top of days' rows
         isWeekend: true, // is it a formal weekend or not?
      },
   ],

   // just play around with this number between 0 and 6
   weekStartingIndex: 6,

   // return a { year: number, month: number, day: number } object
   // eslint-disable-next-line @typescript-eslint/no-explicit-any
   getToday(gregorainTodayObject: any) {
      return gregorainTodayObject;
   },

   // return a native JavaScript date here
   // eslint-disable-next-line @typescript-eslint/no-explicit-any
   toNativeDate(date: any) {
      return new Date(date.year, date.month - 1, date.day);
   },

   // return a number for date's month length
   // eslint-disable-next-line @typescript-eslint/no-explicit-any
   getMonthLength(date: any) {
      return new Date(date.year, date.month, 0).getDate();
   },

   // return a transformed digit to your locale
   // eslint-disable-next-line @typescript-eslint/no-explicit-any
   transformDigit(digit: any) {
      return digit;
   },

   // texts in the date picker
   nextMonth: "Mois suivant",
   previousMonth: "Mois précédent",
   openMonthSelector: "Ouvrir le sélecteur de mois",
   openYearSelector: "Ouvrir le sélecteur d'année",
   closeMonthSelector: "Fermer le sélecteur de mois",
   closeYearSelector: "Fermer le sélecteur d'année",
   defaultPlaceholder: "Sélectionner...",

   // for input range value
   from: "du",
   to: "au",

   // used for input value when multi dates are selected
   digitSeparator: ",",

   // if your provide -2 for example, year will be 2 digited
   yearLetterSkip: 0,

   // is your language rtl or ltr?
   isRtl: false,
};
