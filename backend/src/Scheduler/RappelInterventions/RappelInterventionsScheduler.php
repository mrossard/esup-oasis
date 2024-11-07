<?php

/*
 * Copyright (c) 2024. Esup - Université de Bordeaux.
 *
 * This file is part of the Esup-Oasis project (https://github.com/EsupPortail/esup-oasis).
 *  For full copyright and license information please view the LICENSE file distributed with the source code.
 *
 *  @author Manuel Rossard <manuel.rossard@u-bordeaux.fr>
 *
 */

namespace App\Scheduler\RappelInterventions;

use App\Entity\Parametre;
use App\Repository\ParametreRepository;
use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('rappelHebdo')]
class RappelInterventionsScheduler implements ScheduleProviderInterface
{
    use ClockAwareTrait;

    public const string INTERVALLE = '7 days';

    public function __construct(private readonly ParametreRepository $parametreRepository,
                                private readonly string              $heurePassage,
                                private readonly string              $startScheduleNow)
    {

    }

    public function getSchedule(): Schedule
    {
        $frequency = $this->parametreRepository->findOneBy([
            'cle' => Parametre::FREQUENCE_RAPPELS,
        ]);

        $debut = $this->now();
        $fin = (clone $debut)->modify('+ ' . self::INTERVALLE);

        $from = $this->startScheduleNow == 'true' ? $this->now() : 'sunday 23:45';

        return (new Schedule())->add(
            RecurringMessage::every(frequency: $frequency?->getValeurCourante()->getValeur() ?? self::INTERVALLE,
                                    message  : new RappelInterventionsMessage($debut, $fin),
                                    from     : $from)
        );
    }
}