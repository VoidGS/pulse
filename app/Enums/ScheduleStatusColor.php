<?php

namespace App\Enums;

enum ScheduleStatusColor: int {
    case PENDENTE = 8;
    case CONFIRMADO = 2;
    case CANCELADO = 4;
    case REMARCADO = 6;
    case FINALIZADO = 10;
    case FALTOU = 11;
}