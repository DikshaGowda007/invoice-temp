<?php

namespace App\Modules\V1\Dashboard\Helpers;

use App\Constants\DashboardConstants;
use App\Http\Requests\V1\Dashboard\Summary\DetailsRequest;
use App\Modules\V1\Dashboard\Bo\Summary\DetailsBo;

class DashboardHelper
{
    public function prepareSummaryBo(DetailsRequest $detailsRequest): DetailsBo
    {
        $detailsBo = new DetailsBo;

        $detailsBo->setRange($detailsRequest->input('range', DashboardConstants::DEFAULT_RANGE));

        return $detailsBo;
    }
}
