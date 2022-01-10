<?php

namespace App\Form\Model;

use App\Entity\Execution;
use App\Entity\Transaction;
use Symfony\Component\Validator\Constraints as Assert;

class ExecutionFormModel
{
    public $time;

    public $account;

    public $instrument;

    public $direction;

    public $volume;

    #[Assert\PositiveOrZero]
    public $price;

    public $type;

    #[Assert\PositiveOrZero]
    public $external_id;

    public $notes;

    #[Assert\NegativeOrZero]
    public $commission;

    public $tax;

    #[Assert\NegativeOrZero]
    public $interest;

    public function populateExecution(Execution $execution)
    {
        $execution->setVolume($this->volume);
        $execution->setPrice($this->price);
        $execution->setDirection($this->direction);
        $execution->setType($this->type);
        $this->populateTransaction($execution->getTransaction());
    }

    public function fromExecution(Execution $execution)
    {
        $this->volume = $execution->getVolume();
        $this->price = $execution->getPrice();
        $this->direction = $execution->getDirection();
        $this->type = $execution->getType();
        $this->fromTransaction($execution->getTransaction());
    }
    
    private function populateTransaction(Transaction $transaction)
    {
        $transaction->setTime($this->time);
        $transaction->setAccount($this->account);
        $transaction->setInstrument($this->instrument);
        if ($this->direction == 0)
        {
            $total = $this->volume * $this->price;
            $transaction->setCash($total); // TODO: Currency conversion
        }
        else
        {
            $total = -1 * $this->direction * $this->volume * $this->price;
            $transaction->setPortfolio($total); // TODO: Currency conversion
        }
        if ($this->commission) {
            $transaction->setCommission($this->commission);
        } else {
            $transaction->setCommission(null);
        }
        if ($this->tax) {
            $transaction->setTax($this->tax);
        } else {
            $transaction->setTax(null);
        }
        if ($this->interest) {
            $transaction->setInterest($this->interest);
        } else {
            $transaction->setInterest(null);
        }
        $transaction->setExternalId($this->external_id);
        $transaction->setNotes($this->notes);
    }

    private function fromTransaction(Transaction $transaction)
    {
        $this->time = $transaction->getTime();
        $this->account = $transaction->getAccount();
        $this->instrument = $transaction->getInstrument();
        $this->external_id = $transaction->getExternalId();
        $this->notes = $transaction->getNotes();

        $this->commission = $transaction->getCommission();
        $this->tax = $transaction->getTax();
        $this->interest = $transaction->getInterest();
    }
}

?>