<div class="flex">
    <div class="flex gap-1">
        @if ($paidOffAt === null)
            @if ($paymentStatus === 'SEBAGIAN')
                <div>
                    <div class="rounded p-1 bg-yellow-200 text-yellow-500 font-bold text-center">
                        <div class="min-w-max">{{ date('d',strtotime($createdAt)) }}</div>
                        <div class="min-w-max">{{ date('m-y',strtotime($createdAt)) }}</div>
                    </div>
                </div>
                <div>
                    <div class="rounded p-1 bg-yellow-300 text-yellow-600 font-bold text-center">
                        <div class="min-w-max">{{ date('d',strtotime($invoice->latestAccountingInvoice->created_at)) }}</div>
                        <div class="min-w-max">{{ date('m-y',strtotime($invoice->latestAccountingInvoice->created_at)) }}</div>
                    </div>
                </div>
            @else
                <div>
                    <div class="rounded p-1 bg-pink-200 text-pink-500 font-bold text-center">
                        <div class="min-w-max">{{ date('d',strtotime($createdAt)) }}</div>
                        <div class="min-w-max">{{ date('m-y',strtotime($createdAt)) }}</div>
                    </div>
                </div>
            @endif
        @else
            <div>
                <div class="rounded p-1 bg-sky-200 text-sky-500 font-bold text-center">
                    <div class="min-w-max">{{ date('d',strtotime($createdAt)) }}</div>
                    <div class="min-w-max">{{ date('m-y',strtotime($createdAt)) }}</div>
                </div>
            </div>
        @endif
    </div>
    <div class="flex ml-1 items-center">
        @if ($paidOffAt !== null)
            <div>
                <div class="rounded p-1 bg-emerald-200 text-emerald-500 font-bold text-center">
                    <div class="min-w-max">{{ date('d',strtotime($paidOffAt)) }}</div>
                    <div class="min-w-max">{{ date('m-y',strtotime($paidOffAt)) }}</div>
                </div>
            </div>
        @else
            @if ($paymentStatus !== 'SEBAGIAN')
                <span class="font-bold">--</span> 
            @endif
        @endif
    </div>
</div>