<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\GridController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PixController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductMovementController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SellerRegistrationController;
use App\Http\Controllers\SettingAppearanceController;
use App\Http\Controllers\SettingSystemController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SupplierCatalogController;
use App\Http\Controllers\SupplierCategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierOrderController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TypeReceiptController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookAsaasController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DeliveryGuyController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::post('/broadcasting/auth', function () {
    return Broadcast::auth(request());
})->middleware('auth:sanctum');

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/reset', [UserController::class, 'reset']);
Route::post('/verify', [UserController::class, 'verify']);
Route::post('/seller-registration', [SellerRegistrationController::class, 'store']);
Route::post('/newPassword', [UserController::class, 'newPassword']);
Route::get('/auth/google/redirect', [UserController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [UserController::class, 'handleGoogleCallback']);

Route::middleware(['webhook.asaas'])->group(function () {
    Route::post('/send-webhook', [WebhookAsaasController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'token.expiration'])->group(function () {

    Route::prefix('department')->group(function () {
        Route::get('/', [DepartmentController::class, 'index']);
        Route::post('/', [DepartmentController::class, 'store']);
        Route::put('/', [DepartmentController::class, 'update']);
        Route::delete('/{departmentID}', [DepartmentController::class, 'destroy']);
    });

    Route::prefix('color')->group(function () {
        Route::get('/', [ColorController::class, 'index']);
        Route::post('/', [ColorController::class, 'store']);
        Route::put('/', [ColorController::class, 'update']);
        Route::delete('/{colorID}', [ColorController::class, 'destroy']);
    });

    Route::prefix('movement')->group(function () {
        Route::get('/', [MovementController::class, 'index']);
        Route::get('/periods', [MovementController::class, 'indexPeriod']);
        Route::get('/{movementID}', [MovementController::class, 'show']);
        Route::post('/export', [MovementController::class, 'export']);
        Route::post('/filter', [MovementController::class, 'filter']);
        Route::post('/', [MovementController::class, 'store']);
        Route::put('/', [MovementController::class, 'update']);
        Route::delete('/{movementID}', [MovementController::class, 'destroy']);
    });

    Route::prefix('grid')->group(function () {
        Route::prefix('item')->group(function () {
            Route::get('/{itemID}', [GridController::class, 'showItem']);
            Route::post('/', [GridController::class, 'storeItem']);
            Route::put('/', [GridController::class, 'updateItem']);
            Route::delete('/{itemID}', [GridController::class, 'destroyItem']);
        });

        Route::get('/', [GridController::class, 'index']);
        Route::get('/get-itens/{gridID}', [GridController::class, 'indexItens']);
        Route::post('/', [GridController::class, 'store']);
        Route::put('/', [GridController::class, 'update']);
        Route::delete('/{gridID}', [GridController::class, 'destroy']);
    });

    Route::prefix('supplier')->group(function () {
        Route::prefix('category')->group(function () {
            Route::get('/', [SupplierCategoryController::class, 'index']);
            Route::get('/{categoryID}', [SupplierCategoryController::class, 'show']);
            Route::post('/', [SupplierCategoryController::class, 'store']);
            Route::put('/', [SupplierCategoryController::class, 'update']);
            Route::delete('/{categoryID}', [SupplierCategoryController::class, 'destroy']);
        });

        Route::prefix('order')->group(function () {
            Route::get('/', [SupplierOrderController::class, 'index']);
            Route::get('/history/{orderID}', [SupplierOrderController::class, 'getHistory']);
            Route::get('/{orderID}', [SupplierOrderController::class, 'show']);
            Route::post('/', [SupplierOrderController::class, 'store']);
            Route::post('/export', [SupplierOrderController::class, 'export']);
            Route::put('/', [SupplierOrderController::class, 'update']);
            Route::put('/received', [SupplierOrderController::class, 'received']);
            Route::put('/status', [SupplierOrderController::class, 'updateStatus']);
            Route::delete('/{orderID}', [SupplierOrderController::class, 'destroy']);
        });

        Route::prefix('catalog')->group(function () {
            Route::get('/{supplierID}', [SupplierCatalogController::class, 'index']);
            Route::get('/variant/{variantID}', [SupplierCatalogController::class, 'getByVariant']);
            Route::post('/', [SupplierCatalogController::class, 'store']);
            Route::put('/', [SupplierCatalogController::class, 'update']);
            Route::delete('/{supplierID}/{productVariantID}', [SupplierCatalogController::class, 'destroy']);
        });

        Route::get('/', [SupplierController::class, 'index']);
        Route::get('/list-select', [SupplierController::class, 'list']);
        Route::get('/{supplierID}', [SupplierController::class, 'show']);
        Route::post('/', [SupplierController::class, 'store']);
        Route::post('/filter', [SupplierController::class, 'filter']);
        Route::put('/', [SupplierController::class, 'update']);
        Route::delete('/{supplierID}', [SupplierController::class, 'destroy']);
    });

    Route::prefix('product')->group(function () {
        Route::prefix('category')->group(function () {
            Route::get('/', [ProductCategoryController::class, 'index']);
            Route::get('/{categoryID}', [ProductCategoryController::class, 'show']);
            Route::post('/', [ProductCategoryController::class, 'store']);
            Route::put('/', [ProductCategoryController::class, 'update']);
            Route::delete('/{categoryID}', [ProductCategoryController::class, 'destroy']);
        });

        Route::prefix('variant')->group(function () {
            Route::get('/{variantID}', [ProductController::class, 'showVariant']);
            Route::post('/search', [ProductController::class, 'search']);
            Route::post('/movement', [ProductMovementController::class, 'store']);
            Route::post('/check-codes', [ProductController::class, 'checkCodes']);
            Route::put('/', [ProductController::class, 'updateVariant']);
            Route::delete('/{variantID}', [ProductController::class, 'destroyVariant']);
        });

        Route::prefix('stock-reentry')->group(function () {
            Route::post('/movement', [ProductMovementController::class, 'storeStockReentry']);
        });

        Route::get('/', [ProductController::class, 'index']);
        Route::get('/{productID}', [ProductController::class, 'show']);
        Route::post('/export', [ProductController::class, 'export']);
        Route::post('/', [ProductController::class, 'store']);
        Route::post('/filter', [ProductController::class, 'filter']);
        Route::put('/', [ProductController::class, 'update']);
        Route::post('/update-media', [ProductController::class, 'updateMedia']);
        Route::put('/basic', [ProductController::class, 'updateBasic']);
        Route::put('/advanced', [ProductController::class, 'updateAdvanced']);
        Route::put('/tag', [ProductController::class, 'updateTag']);
        Route::delete('/{productID}', [ProductController::class, 'destroy']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{userID}', [UserController::class, 'show']);
        Route::post('/update-data', [UserController::class, 'updateData']);
        Route::put('/update-password', [UserController::class, 'updatePassword']);
        Route::post('/', [UserController::class, 'store']);
        Route::post('/filter', [UserController::class, 'filter']);
        Route::put('/', [UserController::class, 'update']);
        Route::delete('/{userID}', [UserController::class, 'destroy']);
    });

    Route::prefix('client')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::get('/{clientID}', [ClientController::class, 'show']);
        Route::post('/', [ClientController::class, 'store']);
        Route::post('/filter', [ClientController::class, 'filter']);
        Route::put('/', [ClientController::class, 'update']);
        Route::delete('/{clientID}', [ClientController::class, 'destroy']);
        Route::post('/credit', [ClientController::class, 'getCredit']);
    });

    Route::prefix('tag')->group(function () {
        Route::get('/', [TagController::class, 'index']);
        Route::post('/', [TagController::class, 'store']);
        Route::put('/', [TagController::class, 'update']);
        Route::delete('/{tagID}', [TagController::class, 'destroy']);
    });

    Route::prefix('employee')->group(function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::get('/{employeeID}', [EmployeeController::class, 'show']);
        Route::post('/', [EmployeeController::class, 'store']);
        Route::post('/filter', [EmployeeController::class, 'filter']);
        Route::put('/', [EmployeeController::class, 'update']);
        Route::delete('/{employeeID}', [EmployeeController::class, 'destroy']);

        Route::prefix('action')->group(function () {
            Route::post('/create-access-login', [EmployeeController::class, 'createAccessLogin']);
            Route::post('/remove-access-login', [EmployeeController::class, 'removeAccessLogin']);
        });
    });

    Route::prefix('role')->group(function () {
        Route::get('/list-select', [RoleController::class, 'indexSelect']);
    });

    Route::prefix('transaction')->group(function () {
        Route::prefix('category')->group(function () {
            Route::get('/', [TransactionCategoryController::class, 'index']);
            Route::get('/{categoryID}', [TransactionCategoryController::class, 'show']);
            Route::post('/', [TransactionCategoryController::class, 'store']);
            Route::put('/', [TransactionCategoryController::class, 'update']);
            Route::delete('/{categoryID}', [TransactionCategoryController::class, 'destroy']);
        });
    });

    Route::prefix('receipt')->group(function () {
        Route::prefix('type')->group(function () {
            Route::get('/', [TypeReceiptController::class, 'index']);
            Route::get('/without-credit', [TypeReceiptController::class, 'indexWithoutCredit']);
            Route::post('/filter', [TypeReceiptController::class, 'filter']);
        });

        Route::get('/', [ReceiptController::class, 'index']);
        Route::get('/{receiptID}', [ReceiptController::class, 'show']);
        Route::post('/filter', [ReceiptController::class, 'filter']);
        Route::post('/', [ReceiptController::class, 'store']);
        Route::put('/', [ReceiptController::class, 'update']);
        Route::delete('/{receiptID}', [ReceiptController::class, 'destroy']);
    });

    Route::prefix('setting')->group(function () {
        Route::prefix('appearance')->group(function () {
            Route::put('/', [SettingAppearanceController::class, 'update']);
            Route::get('/', [SettingAppearanceController::class, 'show']);
        });

        Route::prefix('system')->group(function () {
            Route::put('/', [SettingSystemController::class, 'update']);
            Route::get('/', [SettingSystemController::class, 'show']);
        });
    });

    Route::prefix('feedback')->group(function () {
        Route::post('/', [FeedbackController::class, 'store']);
    });

    Route::prefix('schedule')->group(function () {
        Route::get('/', [ScheduleController::class, 'index']);
        Route::get('/periods', [ScheduleController::class, 'indexPeriod']);
        Route::get('/{scheduleID}', [ScheduleController::class, 'show']);
        Route::post('/export', [ScheduleController::class, 'export']);
        Route::post('/filter', [ScheduleController::class, 'filter']);
        Route::post('/', [ScheduleController::class, 'store']);
        Route::post('/finish', [ScheduleController::class, 'finishSchedule']);
        Route::put('/', [ScheduleController::class, 'update']);
        Route::delete('/{scheduleID}', [ScheduleController::class, 'destroy']);
    });

    Route::prefix('notification')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::put('/{notificationID}', [NotificationController::class, 'updateRead']);
        Route::delete('/delete/{notificationID}', [NotificationController::class, 'destroy']);
    });

    Route::prefix('enterprise')->group(function () {
        Route::get('/', [EnterpriseController::class, 'show']);
        Route::put('/', [EnterpriseController::class, 'update']);
        Route::delete('/', [EnterpriseController::class, 'destroy']);
    });

    Route::prefix('sale')->group(function () {
        Route::get('/', [SaleController::class, 'index']);
        Route::get('/{saleID}', [SaleController::class, 'show']);
        Route::post('/', [SaleController::class, 'store']);
        Route::post('/cancel', [SaleController::class, 'update']);
        Route::post('/filter', [SaleController::class, 'filter']);
        Route::delete('/{saleID}', [SaleController::class, 'destroy']);
        Route::get('/cancel/{saleID}', [SaleController::class, 'showCancellation']);
        Route::post('/product/{saleID}', [SaleController::class, 'showProducts']);
        Route::get('/coupon/{saleID}/', [SaleController::class, 'showCouponInfos']);
        Route::post('/export', [SaleController::class, 'export']);
        Route::post('/send-to-email', [SaleController::class, 'sendToEmail']);
    });

    Route::prefix('subscription')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index']);

        Route::prefix('payment')->group(function () {
            Route::post('pix/', [PixController::class, 'store']);
            Route::post('credit-card/', [CreditCardController::class, 'store']);
        });
    });

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::post('/filter', [DashboardController::class, 'filter']);
    });

    Route::prefix('commission')->group(function () {
        Route::post('/', [CommissionController::class, 'index']);
        Route::post('/export', [CommissionController::class, 'export']);
        Route::get('/{saleID}', [CommissionController::class, 'showBySale']);
    });

    Route::prefix('returns')->group(function () {
        Route::get('/{saleID}', [ReturnController::class, 'index']);
        Route::get('/return/{returnID}', [ReturnController::class, 'show']);
        Route::get('/linked/{returnID}', [ReturnController::class, 'showLinked']);
        Route::get('/stock/reentry', [ReturnController::class, 'showStockReentry']);
        Route::post('/', [ReturnController::class, 'store']);
        Route::put('/', [ReturnController::class, 'update']);
        Route::delete('/{saleID}/{returnID}', [ReturnController::class, 'destroy']);
    });

    Route::prefix('exchange')->group(function () {
        Route::get('/{saleID}', [ExchangeController::class, 'index']);
        Route::get('/exchange/{exchangeID}', [ExchangeController::class, 'show']);
        Route::post('/', [ExchangeController::class, 'createExchangePayment']);
        Route::post('/difference', [ExchangeController::class, 'createDifferencePayment']);
        Route::post('/export', [ExchangeController::class, 'export']);
        Route::post('/send-to-email', [ExchangeController::class, 'sendToEmail']);
    });

    Route::prefix('delivery')->group(function () {
        Route::get('/dashboard', [DeliveryController::class, 'getDashboard']);
        Route::get('/{deliveryID}', [DeliveryController::class, 'show']);
        Route::post('/', [DeliveryController::class, 'index']);
        Route::post('/filter', [DeliveryController::class, 'filter']);
        Route::post('/schedule', [DeliveryController::class, 'schedule']);
        Route::post('/partial-delivered', [DeliveryController::class, 'partialDelivered']);
        Route::put('/', [DeliveryController::class, 'update']);
    });

    Route::prefix('delivery-guy')->group(function () {
        Route::get('/', [DeliveryGuyController::class, 'index']);
        Route::get('/{deliveryGuyID}', [DeliveryGuyController::class, 'show']);
        Route::post('/', [DeliveryGuyController::class, 'create']);
        Route::put('/', [DeliveryGuyController::class, 'update']);
        Route::delete('/{deliveryGuyID}', [DeliveryGuyController::class, 'destroy']);
    });
});
