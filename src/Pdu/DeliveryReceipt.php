<?php

declare(strict_types=1);

namespace Smpp\Pdu;

use InvalidArgumentException;
use Smpp\Exceptions\SmppException;
use Smpp\Exceptions\SmppInvalidArgumentException;

/**
 * An extension of a SMS, with data embedded into the message part of the SMS.
 * @author hd@onlinecity.dk
 */
class DeliveryReceipt extends Sms
{
    public int|string    $id;
    public int    $sub;
    public int    $dlvrd;
    public int    $submitDate;
    public int    $doneDate;
    public string $stat;
    public int    $err;
    public string $text;

    /**
     * Parse a delivery receipt formatted as specified in SMPP v3.4 - Appendix B
     * It accepts all chars except space as the message id
     *
     * @throws InvalidArgumentException
     * @throws SmppException
     */
    public function parseDeliveryReceipt(): void
    {
        $numMatches = preg_match(
            '/^id:([^ ]+) sub:(\d{1,3}) dlvrd:(\d{3}) submit date:(\d{10,12}) done date:(\d{10,12}) stat:([A-Z ]{7}) err:(\d{2,3}) text:(.*)$/si',
            $this->message,
            $matches
        );
        if ($numMatches === 0) {
            throw new SmppInvalidArgumentException(
                'Could not parse delivery receipt: '
                . $this->message
                . "\n"
                . bin2hex($this->getBody())
            );
        }

        [
            $matched,
            $this->id,
            $this->sub,
            $this->dlvrd,
            $submitDate,
            $doneDate,
            $this->stat,
            $this->err,
            $this->text
        ] = $matches;

        $this->submitDate = $this->convertDate($submitDate);
        $this->doneDate   = $this->convertDate($doneDate);
    }

    /**
     * @param string $date
     * @return int
     * @throws SmppException
     */
    private function convertDate(string $date): int
    {
        $dateParts = str_split($date, 2);
        $timestamp = gmmktime(
            (int)$dateParts[3],
            (int)$dateParts[4],
            (int)$dateParts[5],
            (int)$dateParts[1],
            (int)$dateParts[2],
            (int)$dateParts[0]
        );

        if ($timestamp === false) {
            throw new SmppException('Invalid date provided');
        }
        return $timestamp;
    }
}
