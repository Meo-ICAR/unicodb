<?php

namespace App\Filament\Resources\ClientMandates\Pages;

use App\Filament\Resources\ClientMandates\ClientMandateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClientMandate extends CreateRecord
{
    protected static string $resource = ClientMandateResource::class;
}
