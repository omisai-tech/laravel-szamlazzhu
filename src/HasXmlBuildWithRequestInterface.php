<?php

namespace Omisai\Szamlazzhu;

interface HasXmlBuildWithRequestInterface
{
    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(SzamlaAgentRequest $request): array;
}
