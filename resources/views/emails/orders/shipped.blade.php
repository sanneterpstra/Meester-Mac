<!DOCTYPE html>
<html lang="nl" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
  <meta charset="utf-8">
  <meta name="x-apple-disable-message-reformatting">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no, date=no, address=no, email=no, url=no">
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <!--[if mso]>
	<noscript>
		<xml>
			<o:OfficeDocumentSettings xmlns:o="urn:schemas-microsoft-com:office:office">
				<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
		</xml>
	</noscript>
	<style>
		td,th,div,p,a,h1,h2,h3,h4,h5,h6 {font-family: "Segoe UI", sans-serif; mso-line-height-rule: exactly;}
	</style>
	<![endif]-->
  <title>{{ $title }}</title>
  <style>
    .hover-bg-blue-600:hover {
      background-color: #2563eb !important
    }
    .hover-important-text-decoration-underline:hover {
      text-decoration: underline !important
    }
    @media (max-width: 600px) {
      .sm-my-8 {
        margin-top: 32px !important;
        margin-bottom: 32px !important
      }
      .sm-px-4 {
        padding-left: 16px !important;
        padding-right: 16px !important
      }
      .sm-px-6 {
        padding-left: 24px !important;
        padding-right: 24px !important
      }
      .sm-leading-8 {
        line-height: 32px !important
      }
    }
  </style>
</head>
<body style="margin: 0; width: 100%; padding: 0; -webkit-font-smoothing: antialiased; word-break: break-word">
  <div role="article" aria-roledescription="email" aria-label="{{ $title || '' }}">
    <div class="sm-px-4" style="background-color: #f8fafc; font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif">
      <table align="center" cellpadding="0" cellspacing="0" role="none">
        <tr>
          <td style="width: 600px; max-width: 100%">
            <div class="sm-my-8" style="margin-top: 48px; margin-bottom: 48px; text-align: center">
              <a href="https://meestermac.nl">
                <img src="{{asset('images/emails/maizzle.png')}}" width="70" alt="Meester Mac" style="max-width: 100%; vertical-align: middle; line-height: 1; border: 0">
              </a>
            </div>
            <table style="width: 100%;" cellpadding="0" cellspacing="0" role="none">
              <tr>
                <td class="sm-px-6" style="border-radius: 4px; background-color: #fff; padding: 48px; font-size: 16px; color: #334155; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05)">
                  <h1 class="sm-leading-8" style="margin: 0; font-size: 24px; font-weight: 600; color: #000">
                    Je pakket is verzonden
                  </h1>
                  <p style="line-height: 4px; color: #94a3b8">#{{ $order->reference }}</p>
                  <div role="separator" style="line-height: 10px; `mso-line-height-alt: ${msoHeight}` }}">&zwj;</div>
                  <p>Het pakket met je bestelling is verzonden. Het wordt zo spoedig mogelijk bezorgd.</p>
                  @if(!empty($order->meta))
                  @if(array_key_exists('tracking_url', $order->meta->toArray()))
                  <a href="{{ $order->meta->toArray()['tracking_url'] }}" class="hover-bg-blue-600" style="display: inline-block; border-radius: 4px; background-color: #3b82f6; padding: 16px 24px; font-size: 14px; font-weight: 600; line-height: 1; color: #fff; text-decoration: none">
                    <!--[if mso]><i style="letter-spacing: 27px; mso-font-width: -100%; mso-text-raise: 26pt;">&nbsp;</i><![endif]-->
                    <span style="mso-text-raise: 13pt;">Volg je pakket</span>
                    <!--[if mso]><i style="letter-spacing: 27px; mso-font-width: -100%;">&nbsp;</i><![endif]-->
                  </a>
                  @endif
                  @endif <div role="separator" style="line-height: 30px; `mso-line-height-alt: ${msoHeight}` }}">&zwj;</div>
                  <div role="separator" style="background-color: #e2e8f0; height: 1px; line-height: 1px; `background-color: $color">&zwj;</div>
                  <table style="width: 100%; table-layout: auto" cellpadding="0" cellspacing="0" role="none">
                    @foreach($order->productLines as $line)
                    <tr>
                      <td style="width: 16.666667%; padding-top: 16px; padding-bottom: 16px">
                        <img src="{{ $line->purchasable?->getThumbnail()->getUrl('small') }}" style="max-width: 100%; vertical-align: middle; line-height: 1; border: 0; height: 100%; width: 100%; border-radius: 8px; object-fit: contain; object-position: center" alt="">
                      </td>
                      <td style="width: 58.333333%; padding-top: 16px; padding-bottom: 16px; padding-left: 16px; vertical-align: top">
                        <p style="margin: 0; color: #111827">
                          @if($line->quantity > 1)
                          {{ $line->quantity}}x
                          @endif
                          {{ $line->description }}
                        </p>
                        <p style="margin: 0; color: #6b7280">{{ $line->option }}</p>
                      </td>
                      <td style="width: 25%; padding-top: 16px; padding-bottom: 16px; text-align: right; vertical-align: top">{{ $line->sub_total->formatted }}</td>
                    </tr>
                    @endforeach
                    <tr>
                      <td colspan="3">
                        <div role="separator" style="background-color: #e2e8f0; height: 1px; line-height: 1px; `background-color: $color;">&zwj;</div>
                      </td>
                    </tr>
                    @foreach($order->shippingLines as $shippingLine)
                    <tr>
                      <td colspan="2" style="width: 83.333333%; padding-top: 16px; padding-bottom: 16px; vertical-align: top">
                        <p style="margin: 0; color: #111827;">{{ $shippingLine->description }}</p>
                      </td>
                      <td style="width: 16.666667%; padding-top: 16px; padding-bottom: 16px; text-align: right; vertical-align: top;">{{ $shippingLine->sub_total->formatted }}</td>
                    </tr>
                    @endforeach
                    <tr>
                      <td colspan="3">
                        <div role="separator" style="background-color: #e2e8f0; height: 1px; line-height: 1px; `background-color: $color;">&zwj;</div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" style="width: 83.333333%; padding-top: 16px; padding-bottom: 16px; vertical-align: top;">
                        <p style="margin: 0; color: #111827;">Totaal</p>
                      </td>
                      <td style="width: 16.666667%; padding-top: 16px; padding-bottom: 16px; text-align: right; vertical-align: top;">{{ $order->total->formatted }}</td>
                    </tr>
                  </table>
                  <div role="separator" style="background-color: #e2e8f0; height: 1px; line-height: 1px; `background-color: $color;">&zwj;</div>
                  <div role="separator" style="line-height: 40px; `mso-line-height-alt: ${msoHeight}` }}">&zwj;</div>
                  <table style="width: 100%; table-layout: auto;" cellpadding="0" cellspacing="0" role="none">
                    <tr>
                      <td>
                        <h2 style="font-size: 18px">Afleveradres:</h2>
                      </td>
                      <td>
                        <h2 style="font-size: 18px;">Factuuradres:</h2>
                      </td>
                    </tr>
                    <tr>
                      <td>{{$order->shippingAddress->first_name}} {{$order->shippingAddress->last_name}}</td>
                      <td>{{$order->billingAddress->first_name}} {{$order->billingAddress->last_name}}</td>
                    </tr>
                    <tr>
                      <td>{{$order->shippingAddress->line_one}}</td>
                      <td>{{$order->billingAddress->line_one}}</td>
                    </tr>
                    <tr>
                      <td>{{$order->shippingAddress->postcode}} {{$order->shippingAddress->city}}</td>
                      <td>{{$order->billingAddress->postcode}} {{$order->billingAddress->city}}</td>
                    </tr>
                  </table>
                  <div role="separator" style="line-height: 40px; `mso-line-height-alt: ${msoHeight}` }}">&zwj;</div>
                  <p style="margin: 0;">
                    Heel veel plezier met je aankoop! Heb je vragen over je bestelling neem dan contact op. Ik help je graag verder.
                    <br>
                    <br>
                    Met vriendelijke groeten, <br>
                    Meester Mac
                  </p>
                </td>
              </tr>
              <tr role="separator">
                <td style="line-height: 48px">&zwj;</td>
              </tr>
              <tr>
                <td style="padding-left: 24px; padding-right: 24px; text-align: center; font-size: 12px; color: #475569">
                  <p style="cursor: default">
                    <a href="https://meestermac.nl/" class="hover-important-text-decoration-underline" style="color: #1d4ed8; text-decoration: none">Meester Mac</a>
                    &bull;
                    <a href="https://meestermac.nl/contact" class="hover-important-text-decoration-underline" style="color: #1d4ed8; text-decoration: none;">Contact</a>
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </div>
</body>
</html>