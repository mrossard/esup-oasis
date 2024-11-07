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

namespace App\Scheduler\TestMailer;

use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('testMail')]
class TestMailerScheduler implements ScheduleProviderInterface
{
    use ClockAwareTrait;

    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            RecurringMessage::every(frequency: '1 hour',
                                    message  : new TestMailerMessage(),
                                    from     : $this->now())
        );
    }
}