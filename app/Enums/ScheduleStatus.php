<?php

namespace App\Enums;

enum ScheduleStatus: string {
    case PENDENTE = 'pendente';
    case CONFIRMADO = 'confirmado';
    case CANCELADO = 'cancelado';
    case REMARCADO = 'remarcado';
    case FINALIZADO = 'finalizado';
    case FALTOU = 'faltou';
}