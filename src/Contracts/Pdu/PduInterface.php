<?php

declare(strict_types=1);


namespace Smpp\Contracts\Pdu;


interface PduInterface
{
    public function getId(): int|string;
    public function getSequence(): int|string;
    public function getStatus(): int|string;
    public function getBody(): int|string;
}
