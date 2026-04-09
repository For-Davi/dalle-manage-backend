<?php

namespace App\Services;

use App\DTO\Dashboard\FilterDashboardDTO;
use App\Repositories\ClientRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\GridGroupRepository;
use App\Repositories\MovementRepository;
use App\Repositories\ProductVariantRepository;
use App\Repositories\ReceiptRepository;
use App\Repositories\SaleItemRepository;
use App\Repositories\SaleRepository;
use App\Repositories\SupplierRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function __construct(
        protected SaleRepository $saleRepository,
        protected MovementRepository $movementRepository,
        protected SaleItemRepository $saleItemRepository,
        protected ClientRepository $clientRepository,
        protected EmployeeRepository $employeeRepository,
        protected ProductVariantRepository $productVariantRepository,
        protected ReceiptRepository $receiptRepository,
        protected SupplierRepository $supplierRepository,
        protected UserRepository $userRepository,
        protected GridGroupRepository $gridGroupRepository
    ) {}

    public function getInfo()
    {
        $currentYear = now('America/Sao_Paulo')->year;

        $sales = $this->saleRepository->getAllWithFilter([
            'start_date' => "01/01/{$currentYear}",
            'end_date' => "31/12/{$currentYear}",
            'enterprise_id' => Auth::user()->enterprise_id,
        ]);

        $movements = $this->movementRepository->getMovementForDashboard([
            'start_date' => "01/01/{$currentYear}",
            'end_date' => "31/12/{$currentYear}",
            'enterprise_id' => Auth::user()->enterprise_id,
        ]);

        $sales->load([
            'items.product.product.category',
            'payment.type',
        ]);

        $saleItems = $sales->flatMap(fn ($sale) => $sale->items);
        $salePayments = $sales->flatMap(fn ($sale) => $sale->payment);

        $salesMade = $sales->count();
        $salesValue = $this->getTotalValue($sales, 'current_total', null, null, 'change');
        $movementsEntryValue = $this->getTotalValue($movements, 'value', 'type', 'entry');
        $movementsOutValue = $this->getTotalValue($movements, 'value', 'type', 'out');
        if ($salesMade !== 0) {
            $mediumTicket = $salesValue / $salesMade;
        } else {
            $mediumTicket = 0;
        }
        $categoriesMostSold = $this->getCategoriesMostSold($saleItems);
        $receiptsValue = $this->getTypeReceiptsValue($salePayments);
        $records = $this->getAllRecords();
        $salesMonthsInfo = $this->getSalesAllMonth($sales);
        $products = $this->getProductsInfo($saleItems);
        $sellers = $this->getSellerInfo($sales);

        $data = [
            'sales_made' => $salesMade,
            'sales_value' => $salesValue,
            'movements_entry_value' => $movementsEntryValue,
            'movements_out_value' => $movementsOutValue,
            'medium_ticket' => $mediumTicket,
            'categories_most_sold' => $categoriesMostSold,
            'receipts_value' => $receiptsValue,
            'records' => $records,
            'sales_months_info' => $salesMonthsInfo,
            'products' => $products,
            'sellers' => $sellers,
        ];

        return $data;
    }

    public function getInfoFilter($request)
    {
        $filterDashboardDTO = FilterDashboardDTO::fromRequest($request);

        $sales = $this->saleRepository->getAllWithFilter($filterDashboardDTO->toArray());
        $movements = $this->movementRepository->getMovementForDashboard($filterDashboardDTO->toArray());

        $saleItems = $sales->flatMap(fn ($sale) => $sale->items);
        $salePayments = $sales->flatMap(fn ($sale) => $sale->payment);
        $salesMade = $sales->count();
        $salesValue = $this->getTotalValue($sales, 'total', null, null, 'change');
        $movementsEntryValue = $this->getTotalValue($movements, 'value', 'type', 'entry');
        $movementsOutValue = $this->getTotalValue($movements, 'value', 'type', 'out');

        if ($salesMade !== 0) {
            $mediumTicket = $salesValue / $salesMade;
        } else {
            $mediumTicket = 0;
        }

        $categoriesMostSold = $this->getCategoriesMostSold($saleItems);
        $receiptsValue = $this->getTypeReceiptsValue($salePayments, 'payment_method_id', $filterDashboardDTO->type_receipt);
        $records = $this->getAllRecords();
        $salesMonthsInfo = $this->getSalesAllMonth($sales);

        if ($filterDashboardDTO->category && $filterDashboardDTO->product) {
            $products = $this->getProductsInfo($saleItems, 'product_name', 'product_category_id', $filterDashboardDTO->product, $filterDashboardDTO->category);
        } elseif ($filterDashboardDTO->category) {
            $products = $this->getProductsInfo($saleItems, null, 'product_category_id', null, $filterDashboardDTO->category);
        } elseif ($filterDashboardDTO->product) {
            $products = $this->getProductsInfo($saleItems, 'product_name', null, $filterDashboardDTO->product, null);
        } else {
            $products = $this->getProductsInfo($saleItems);
        }

        $sellers = $this->getSellerInfo($sales);

        $data = [
            'sales_made' => $salesMade,
            'sales_value' => $salesValue,
            'movements_entry_value' => $movementsEntryValue,
            'movements_out_value' => $movementsOutValue,
            'medium_ticket' => $mediumTicket,
            'categories_most_sold' => $categoriesMostSold,
            'receipts_value' => $receiptsValue,
            'records' => $records,
            'sales_months_info' => $salesMonthsInfo,
            'products' => $products,
            'sellers' => $sellers,
        ];

        return $data;
    }

    private function getTotalValue($array, $field, $fieldCondition = null, $condition = null, $subtractionField = null)
    {
        $total = 0;
        foreach ($array as $data) {
            if ($fieldCondition !== null && $condition !== null && $condition !== $data->$fieldCondition) {
                continue;
            }
            if ($subtractionField !== null) {
                $total += $data->$field - $data->$subtractionField;
            } else {
                $total += $data->$field;
            }
        }

        return $total;
    }

    private function getCategoriesMostSold($items)
    {
        $categories = [];

        foreach ($items as $item) {
            $variant = $item->product;

            $product = optional($variant)->product;

            $category = optional($product)->category;

            if ($category) {
                $quantity = $item->quantity;
                $catID = $category->id;
                if (! isset($categories[$catID])) {

                    $categories[$catID] = [
                        'id' => $catID,
                        'name' => $category->name,
                        'total_quantity' => 0,
                    ];
                }
                $categories[$catID]['total_quantity'] += $quantity;
            }
        }

        return collect($categories)->sortByDesc('total_quantity')->take(5)->values()->all();
    }

    private function getTypeReceiptsValue($payments, $fieldCondition = null, $filter = null)
    {
        $receipt = [];
        foreach ($payments as $payment) {
            $type = $payment->type;
            if ($fieldCondition !== null && $filter !== null) {
                if ($filter !== $payment->$fieldCondition) {
                    continue;
                }
            }
            if ($type) {
                $typeID = $type->id;
                $quantity = $payment->value;
                if ($payment->installments) {
                    $quantity = $payment->installments * $payment->value;
                }

                if (! isset($receipt[$typeID])) {
                    $receipt[$typeID] = [
                        'id' => $typeID,
                        'name' => $type->name,
                        'total_quantity' => 0,
                    ];
                }
                $receipt[$typeID]['total_quantity'] += $quantity;
            }
        }

        return $receipt;
    }

    private function getAllRecords()
    {
        $clients = $this->clientRepository->getAllByEnterprise();
        $employees = $this->employeeRepository->getAllByEnterprise();
        $products = $this->productVariantRepository->getAllByEnterprise();
        $grids = $this->gridGroupRepository->getAllByEnterprise();
        $receipts = $this->receiptRepository->getAllByEnterprise();
        $suppliers = $this->supplierRepository->getAllByEnterprise();
        $users = $this->userRepository->getAllByEnterprise();
        $movements = $this->movementRepository->getAllByEnterprise();

        return [
            [
                'name' => 'Clientes',
                'quantity' => $clients->count(),
            ],
            [
                'name' => 'Funcionários',
                'quantity' => $employees->count(),
            ],
            [
                'name' => 'Produtos',
                'quantity' => $products->count(),
            ],
            [
                'name' => 'Grades',
                'quantity' => $grids->count(),
            ],
            [
                'name' => 'Recebimentos',
                'quantity' => $receipts->count(),
            ],
            [
                'name' => 'Fornecedores',
                'quantity' => $suppliers->count(),
            ],
            [
                'name' => 'Usuários',
                'quantity' => $users->count(),
            ],
            [
                'name' => 'Movimentações',
                'quantity' => $movements->count(),
            ],
        ];
    }

    private function getSellerInfo($sales)
    {
        $sellersQuantity = [];
        $sellersValue = [];

        foreach ($sales as $sale) {
            $seller = $sale->seller;

            if ($seller) {
                $sellerName = $seller->name;
                $saleSellerValue = $sale->current_total - $sale->change;

                if (! isset($sellersQuantity[$sellerName])) {
                    $sellersQuantity[$sellerName] = 0;
                }
                if (! isset($sellersValue[$sellerName])) {
                    $sellersValue[$sellerName] = 0.0;
                }

                $sellersQuantity[$sellerName] += 1;
                $sellersValue[$sellerName] += $saleSellerValue;
            }
        }

        return [
            'labels' => array_keys($sellersQuantity),
            'quantity' => array_values($sellersQuantity),
            'value' => array_values($sellersValue),
        ];
    }

    private function getProductsInfo($items, $firstConditionField = null, $secondConditionField = null, $firstFilter = null, $secondFilter = null)
    {
        $productsQuantity = [];
        $productsValue = [];

        foreach ($items as $item) {
            if ($firstFilter !== null && $firstConditionField !== null) {
                if (($item->$firstConditionField ?? null) !== $firstFilter) {
                    continue;
                }
            }
            if ($secondFilter !== null && $secondConditionField !== null) {
                if (($item->product->product->$secondConditionField ?? null) !== $secondFilter) {
                    continue;
                }
            }
            $productName = $item->product_name;
            $productQuantity = $item->quantity;
            $productValue = $item->total;

            if (! isset($productsQuantity[$productName])) {
                $productsQuantity[$productName] = 0;
            }
            if (! isset($productsValue[$productName])) {
                $productsValue[$productName] = 0.0;
            }

            $productsQuantity[$productName] += $productQuantity;
            $productsValue[$productName] += $productValue;
        }

        return [
            'labels' => array_keys($productsQuantity),
            'quantity' => array_values($productsQuantity),
            'value' => array_values($productsValue),
        ];
    }

    private function getSalesAllMonth($sales)
    {
        if ($sales->isEmpty()) {
            return [
                'quantity' => [],
                'total' => [],
            ];
        }
        $sales = $sales->sortBy('date');

        $tz = 'America/Sao_Paulo';

        $years = $sales->map(fn ($sale) => Carbon::parse($sale->date, $tz)->year)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $quantity = [];
        $total = [];

        foreach ($years as $year) {
            $quantity[$year] = [
                'label' => (string) $year,
                'data' => array_fill(0, 12, 0),
            ];

            $total[$year] = [
                'label' => (string) $year,
                'data' => array_fill(0, 12, 0.0),
            ];
        }

        foreach ($sales as $sale) {
            $date = Carbon::parse($sale->date, $tz);
            $year = $date->year;
            $monthIndex = $date->month - 1;

            if (isset($quantity[$year])) {
                $quantity[$year]['data'][$monthIndex]++;
                $total[$year]['data'][$monthIndex] += (float) $sale->current_total - $sale->change;
            }
        }

        return [
            'quantity' => $quantity,
            'total' => $total,
        ];
    }
}
