<?php

namespace App\Actions\POS;

use App\Models\CashMovement;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ProcessSaleAction
{
    public function execute(array $cart, array $paymentForm, $table = null)
    {
        return DB::transaction(function () use ($cart, $paymentForm, $table) {
            $sale = $this->createSale($cart, $paymentForm, $table);
            $this->createSaleItems($sale, $cart);
            $this->updateStock($sale, $cart);
            $this->createCashMovement($sale);

            return $sale;
        });
    }

    private function createSale(array $cart, array $paymentForm, $table = null): Sale
    {
        $activeShift = auth()->user()->getActiveShift();
        $company = auth()->user()->company;
        $subtotal = collect($cart)->sum('total_price');
        $taxAmount = $subtotal * ($company->tax_rate / 100);

        return Sale::create([
            'invoice_number' => $company->generateInvoiceNumber(),
            'table_id' => $table?->id,
            'shift_id' => $activeShift->id,
            'user_id' => auth()->id(),
            'sold_at' => now(),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $paymentForm['discount_amount'] ?? 0,
            'service_charge' => $paymentForm['service_charge'] ?? 0,
            'total' => $paymentForm['total_amount'],
            'payment_method' => $paymentForm['payment_method'],
            'payment_details' => $paymentForm['payment_method'] === 'mixed' 
                ? ($paymentForm['mixed_payments'] ?? null) 
                : null,
            'status' => 'completed',
            'notes' => $paymentForm['notes'] ?? '',
            'sale_type' => 'dine_in',
            'customer_count' => $paymentForm['customer_count'] ?? 1,
            'company_id' => auth()->user()->company_id,
            'completed_at' => now()
        ]);
    }

    private function createSaleItems(Sale $sale, array $cart): void
    {
        foreach ($cart as $item) {
            $sale->saleItems()->create([
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
                'company_id' => auth()->user()->company_id
            ]);
        }
    }

    private function updateStock(Sale $sale, array $cart): void
    {
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            $previousStock = $product->stock_quantity;
            $newStock = $previousStock - $item['quantity'];

            $product->update(['stock_quantity' => $newStock]);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'out',
                'quantity' => $item['quantity'],
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'reason' => 'sale',
                'notes' => "Venda #{$sale->invoice_number}",
                'date' => now(),
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id
            ]);
        }
    }

    private function createCashMovement(Sale $sale): void
    {
        $activeShift = auth()->user()->getActiveShift();

        CashMovement::create([
            'shift_id' => $activeShift->id,
            'type' => 'in',
            'amount' => $sale->total,
            'description' => "Venda #{$sale->invoice_number}",
            'category' => 'sale',
            'reference_type' => Sale::class,
            'reference_id' => $sale->id,
            'date' => now(),
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id
        ]);

        $activeShift->increment('total_sales', $sale->total);
        $activeShift->increment('total_orders');
    }
}