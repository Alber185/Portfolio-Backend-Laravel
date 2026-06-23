<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserContactResource extends JsonResource
{
    /**
     * It returns only the contact information of the user.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $contactData = [
            'phone1' => $this->phone1,
            'phone2' => $this->phone2,
            'contactEmail1' => $this->contactEmail1,
            'contactEmail2' => $this->contactEmail2,
            'contactEmail3' => $this->contactEmail3,
            'github' => $this->github,
            'linkedin' => $this->linkedin,
        ];

        return $contactData;
    }
}
