<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sale\CreateSaleRequest;
use App\Http\Requests\Sale\ExportSaleRequest;
use App\Http\Requests\Sale\SendToEmailRequest;
use App\Http\Requests\Sale\ShowSaleRequest;
use Illuminate\Http\Request;
use App\Repositories\SaleRepository;
use App\Services\SaleService;
use App\Utils\ErrorLogger;
use Illuminate\Support\Facades\DB;

class SaleController
{
    public function __construct(
        private SaleService $service,
        private SaleRepository $repository,
    ) {}

    public function index(Request $request)
    {
        try {
            $sales = $this->service->getSales();

            return response()->json(['sales' => $sales], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao fazer busca de vendas:', $e, $request);

            return response()->json(['message' => 'Erro ao fazer busca de vendas'], 500);
        }
    }

    public function store(CreateSaleRequest $request)
    {
        try {
            DB::beginTransaction();
            $sale = $this->service->create($request);
            if ($sale) {
                DB::commit();

                return response()->json(['sale' => $sale, 'message' => 'Venda realizada'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao fazer a venda:', $e, $request);

            return response()->json(['message' => 'Erro ao fazer a venda'], 500);
        }
    }

    public function showCouponInfos(ShowSaleRequest $request)
    {
        try {
            $couponData = $this->repository->getCouponInfos($request->route('saleID'));

            return response()->json(['couponData' => $couponData], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao localizar os itens da venda:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function export(ExportSaleRequest $request)
    {
        try {
            return $this->service->export($request);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao exportar venda:', $e, $request);

            return response()->json(['message' => 'Erro ao exportar venda'], 500);
        }
    }

    public function sendToEmail(SendToEmailRequest $request)
    {
        try {
            $result = $this->service->sendToEmail($request);

            return response()->json(['message' => $result], 200);
        } catch (\Exception $e) {

            ErrorLogger::log('Erro ao enviar cupom para o email:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
