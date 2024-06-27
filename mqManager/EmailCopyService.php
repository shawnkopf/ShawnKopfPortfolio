<?php

namespace App\Services;

use App\Models\EmailCopy;

class EmailCopyService
{
    public function getEmailCopies(): array
    {
        $emailCopy = [
            'received' => '',
            'staging' => '',
            'binding' => '',
            'shipped' => '',
            'empty' => ''
        ];
        $allCopy = EmailCopy::all();
        foreach ($allCopy as $copy) {
            $emailCopy[$copy->status_name] = $copy;
        }

        return $emailCopy;
    }

}
