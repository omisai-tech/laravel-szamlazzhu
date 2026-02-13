<?php

namespace Omisai\Szamlazzhu;

interface HasXmlBuildInterface
{
    /**
     * @throws SzamlaAgentException
     */
    public function buildXmlData(): array;
}
