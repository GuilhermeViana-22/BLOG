<?php

namespace App\Interfaces;

interface AppRequestInterface
{
    public function getAuthorizationHeader(): ?string;
}
