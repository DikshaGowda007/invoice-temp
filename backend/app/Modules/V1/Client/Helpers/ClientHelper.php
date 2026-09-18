<?php

namespace App\Modules\V1\Client\Helpers;

use App\Http\Requests\V1\Client\Add\DetailsRequest as AddDetailsRequest;
use App\Http\Requests\V1\Client\Edit\DetailsRequest as EditDetailsRequest;
use App\Modules\V1\Client\Bo\Add\DetailsBo as AddDetailsBo;
use App\Modules\V1\Client\Bo\Edit\DetailsBo as EditDetailsBo;
use App\Repositories\DAO\V1\ClientDAO;

class ClientHelper
{
    public function prepareAddClientBo(AddDetailsRequest $addDetailsRequest): AddDetailsBo
    {
        $addDetailsBo = new AddDetailsBo;

        $addDetailsBo->setName($addDetailsRequest->input('name'));

        if ($addDetailsRequest->has('email')) {
            $addDetailsBo->setEmail($addDetailsRequest->input('email'));
        }
        if ($addDetailsRequest->has('phone')) {
            $addDetailsBo->setPhone($addDetailsRequest->input('phone'));
        }
        if ($addDetailsRequest->has('address')) {
            $addDetailsBo->setAddress($addDetailsRequest->input('address'));
        }
        if ($addDetailsRequest->has('notes')) {
            $addDetailsBo->setNotes($addDetailsRequest->input('notes'));
        }

        return $addDetailsBo;
    }

    public function prepareEditClientBo(EditDetailsRequest $editDetailsRequest): EditDetailsBo
    {
        $editDetailsBo = new EditDetailsBo;

        $editDetailsBo->setId((int) $editDetailsRequest->input('id'));
        $editDetailsBo->setName($editDetailsRequest->input('name'));

        if ($editDetailsRequest->has('email')) {
            $editDetailsBo->setEmail($editDetailsRequest->input('email'));
        }
        if ($editDetailsRequest->has('phone')) {
            $editDetailsBo->setPhone($editDetailsRequest->input('phone'));
        }
        if ($editDetailsRequest->has('address')) {
            $editDetailsBo->setAddress($editDetailsRequest->input('address'));
        }
        if ($editDetailsRequest->has('notes')) {
            $editDetailsBo->setNotes($editDetailsRequest->input('notes'));
        }

        return $editDetailsBo;
    }

    public function prepareAddClientDao(AddDetailsBo $addDetailsBo, int $userId): ClientDAO
    {
        $clientDao = new ClientDAO;

        $clientDao->setUserId($userId);
        $clientDao->setName($addDetailsBo->getName());
        $clientDao->setEmail($addDetailsBo->getEmail());
        $clientDao->setPhone($addDetailsBo->getPhone());
        $clientDao->setAddress($addDetailsBo->getAddress());
        $clientDao->setNotes($addDetailsBo->getNotes());

        return $clientDao;
    }

    public function prepareEditClientDao(EditDetailsBo $editDetailsBo): ClientDAO
    {
        $clientDao = new ClientDAO;

        $clientDao->setName($editDetailsBo->getName());
        $clientDao->setEmail($editDetailsBo->getEmail());
        $clientDao->setPhone($editDetailsBo->getPhone());
        $clientDao->setAddress($editDetailsBo->getAddress());
        $clientDao->setNotes($editDetailsBo->getNotes());

        return $clientDao;
    }
}
