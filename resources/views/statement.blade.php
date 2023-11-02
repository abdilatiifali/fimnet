<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="robots" content="noindex,nofollow" />
<meta name="viewport" content="width=device-width; initial-scale=1.0;" />

</head>

<style>

  /*
    Common invoice styles. These styles will work in a browser or using the HTML
    to PDF anvil endpoint.
  */

  body {
    font-size: 16px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  table tr td {
    padding: 0;
  }

  table tr td:last-child {
    text-align: right;
  }

  .bold {
    font-weight: bold;
  }

  .right {
    text-align: right;
  }

  .large {
    font-size: 1.75em;
  }

  .total {
    font-weight: bold;
    color: #fb7578;
  }

  .logo-container {
    margin: 20px 0 70px 0;
  }

  .invoice-info-container {
    font-size: 0.875em;
  }
  .invoice-info-container td {
    padding: 4px 0;
  }

  .client-name {
    font-size: 1.5em;
    vertical-align: top;
  }

  .line-items-container {
    margin: 70px 0;
    font-size: 0.875em;
  }

  .line-items-container th {
    text-align: left;
    color: #999;
    border-bottom: 2px solid #ddd;
    padding: 10px 0 15px 0;
    font-size: 0.75em;
    text-transform: uppercase;
  }

  .line-items-container th:last-child {
    text-align: right;
  }

  .line-items-container td {
    padding: 15px 0;
  }

  .line-items-container tbody tr:first-child td {
    padding-top: 25px;
  }

  .line-items-container.has-bottom-border tbody tr:last-child td {
    padding-bottom: 25px;
    border-bottom: 2px solid #ddd;
  }

  .line-items-container.has-bottom-border {
    margin-bottom: 0;
  }

  .line-items-container th.heading-quantity {
    width: 50px;
  }
  .line-items-container th.heading-price {
    text-align: right;
    width: 100px;
  }
  .line-items-container th.heading-subtotal {
    width: 100px;
  }

  .payment-info {
    width: 38%;
    font-size: 0.75em;
    line-height: 1.5;
  }

  .footer {
    margin-top: 100px;
  }

  .footer-thanks {
    font-size: 1.125em;
  }

  .footer-thanks img {
    display: inline-block;
    position: relative;
    top: 1px;
    width: 16px;
    margin-right: 4px;
  }

  .footer-info {
    float: right;
    margin-top: 5px;
    font-size: 0.75em;
    color: #ccc;
  }

  .footer-info span {
    padding: 0 5px;
    color: black;
  }

  .footer-info span:last-child {
    padding-right: 0;
  }

  .page-container {
    display: none;
  }
    /*
    The styles here for use when generating a PDF invoice with the HTML code.
    * Set up a repeating page counter
    * Place the .footer-info in the last page's footer
  */

  .footer {
    margin-top: 30px;
  }

  .footer-info {
    float: none;
    position: running(footer);
    margin-top: -25px;
  }

  .page-container {
    display: block;
    position: running(pageContainer);
    margin-top: -25px;
    font-size: 12px;
    text-align: right;
    color: #999;
  }

  .page-container .page::after {
    content: counter(page);
  }

  .page-container .pages::after {
    content: counter(pages);
  }

  @page {
    @bottom-right {
      content: element(pageContainer);
    }
    @bottom-left {
      content: element(footer);
    }
  }
  </style>

<body>

  <div class="page-container">
  Page
  <span class="page"></span>
  of
  <span class="pages"></span>
</div>

<div class="logo-container">
  <img
    style="height: 150; width: 150;"
    src="{{ $logo }}"
  >
</div>

<table class="invoice-info-container">
  <tr>
    <td rowspan="2" class="client-name">
      {{ $customer->name }}
    </td>
    <td>
      {{ config('app.name')  }}
    </td>
  </tr>
  <tr>
    <td>
      {{ config('app.company.address') }}
    </td>
  </tr>
  <tr>
    <td>
      Statement Date: <strong>{{ now() }}</strong>
    </td>
    <td>
      Nairobi, Kenya
    </td>
  </tr>
  <tr>
    <td>
      Invoice No: <strong>12345</strong>
    </td>
    <td>
      {{ config('app.company.domain') }}
    </td>
  </tr>
</table>

  @foreach ($groupedSubscriptions as $sessionId => $subscriptions)
    <table class="line-items-container">
          <h1>{{ \App\Models\Session::findOrFail($sessionId)->year }}</h1>

        <thead>
          <tr>
            <th class="heading-quantity">Qty</th>
            <th class="heading-description">Description</th>
            <th class="heading-price">Amount</th>
            <th class="heading-price">Amount Paid</th>
            <th class="heading-subtotal">Paid</th>
          </tr>
        </thead>
        <tbody>
          @foreach($subscriptions as $subscription)
            <tr>
              <td>1</td>
              <td>{{ \App\Enums\Month::from($subscription->month_id)->name }}</td>
              <td class="right" style="font-weight: 600;">{{ number_format($subscription->amount) }}</td>
              <td class="right" style="font-weight: 600;">{{ number_format($subscription->amount_paid)}}</td>
              <td class="bold">
                @if ($subscription->paid) 
                  <span style="color: green;">Yes</span>
                @else
                  <span style="color: red;">No</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
    </table>

  @endforeach


<table class="line-items-container has-bottom-border">
  <thead>
    <tr>
      <th>Payment Info</th>
      <th>Due By</th>
      <th>Total Due</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="payment-info">
        <div>
          Paybill No: <strong>{{ config('mpesa.shortCode') }}</strong>
        </div>
        <div>
          Account No: <strong>{{ $customer->mpesaId }}</strong>
        </div>
      </td>
      <td class="large"></td>
      <td class="large total" style="color: orange;">
        {{ number_format($customer->balance()) }}
      </td>
    </tr>
  </tbody>
</table>

<div class="footer">
  <div class="footer-info">
    <span> {{ config('app.name') }} </span> |
    <span> {{ config('app.company.phone') }} </span> |
    <span>{{ config('app.company.domain') }}</span>
  </div>
  <div class="footer-thanks">
{{--     <img src="https://github.com/anvilco/html-pdf-invoice-template/raw/main/img/heart.png" alt="heart">
 --}}    <span>Thank you!</span>
  </div>
</div>
  
</body>
</html>
