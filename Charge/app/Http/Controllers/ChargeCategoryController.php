<?php

namespace App\Http\Controllers;

use App\Models\ChargeCategory;
use App\Repository\ChargeCategoryRepositoryInterface;

class ChargeCategoryController extends Controller
{
    private $chargeCategoryRepository;

    public function __construct(ChargeCategoryRepositoryInterface $chargeCategoryRepository)
    {
        $this->chargeCategoryRepository = $chargeCategoryRepository;
    }

    public function index() {
        $charge_categories = $this->chargeCategoryRepository->index();

        return response()->json(['message' => 'charge category retrieved!', 'body' => $charge_categories, 'error' => false], 200);
    }
}
