<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Mail\NewCustomer;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if(!auth()->user()->isAdmin()){
            $data['employee_id'] = auth()->user()->id;
        }
        if($data['same_as_fatt'] == true){
            $data['stato_c'] = $data['stato_f'];
            $data['prov_c'] = $data['prov_f'];
            $data['citta_c'] = $data['citta_f'];
            $data['cap_c'] = $data['cap_f'];
            $data['via_c'] = $data['via_f'];
        }

        return $data;
    }

    /*protected function afterCreate() : void {
        $customer = $this->record;
        Mail::to(env('MAIL_AD_DEF','mp.ponti84@gmail.com'))->send(new NewCustomer($customer));
    }

    /*protected function getRedirectUrl(): string
    {
        //$response = $this->getResource()::getUrl('index');
        //if(auth()->user()->isAdmin())
        $response = $this->getResource()::getUrl('view');
        return $response;
    }
    */
}
