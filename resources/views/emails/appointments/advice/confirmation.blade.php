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
                    Beste {{ $steps['advice-appointment-contact-info']['fields']['first_name'] }},
                  </h1>
                  <div role="separator" style="line-height: 10px; `mso-line-height-alt: ${msoHeight}` }}">&zwj;</div>
                  <p>Bedankt voor het aanvragen van een adviesgesprek bij Meester Mac. Je aanvraag is succesvol ontvangen. Binnen een werkdag ontvang je een bevestiging van de belafspraak of een voorstel voor een andere datum/tijdstip.</p>
                  <div role="separator" style="line-height: 24px; `mso-line-height-alt: ${msoHeight}` }}">&zwj;</div>
                  <div role="separator" style="background-color: #e2e8f0; height: 1px; line-height: 1px; `background-color: $color">&zwj;</div>
                  <table style="width: 100%; table-layout: auto" cellpadding="0" cellspacing="0" role="none">
                    <tr>
                      <td style="width: 33.333333%; padding-top: 16px; padding-bottom: 16px; vertical-align: top">
                        <p style="margin: 0; color: #111827">Bericht:</p>
                      </td>
                      <td style="width: 66.666667%; padding-top: 16px; padding-bottom: 16px; vertical-align: top">{{ $steps['advice-appointment-date-time-step']['fields']['message'] }}</td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <div role="separator" style="background-color: #e2e8f0; height: 1px; line-height: 1px; `background-color: $color;">&zwj;</div>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 33.333333%; padding-top: 16px; padding-bottom: 16px; vertical-align: top;">
                        <p style="margin: 0; color: #111827;">Telefoonnummer:</p>
                      </td>
                      <td style="width: 66.666667%; padding-top: 16px; padding-bottom: 16px; vertical-align: top;">{{ $steps['advice-appointment-contact-info']['fields']['phone'] }}</td>
                    </tr>
                    <tr>
                      <td colspan="2">
                        <div role="separator" style="background-color: #e2e8f0; height: 1px; line-height: 1px; `background-color: $color;">&zwj;</div>
                      </td>
                    </tr>
                    <tr>
                      <td style="width: 33.333333%; padding-top: 16px; padding-bottom: 16px; vertical-align: top;">
                        <p style="margin: 0; color: #111827;">Datum/tijd:</p>
                      </td>
                      <td style="width: 66.666667%; padding-top: 16px; padding-bottom: 16px; vertical-align: top;">{{ $steps['advice-appointment-date-time-step']['fields']['date'] }} om {{ $steps['advice-appointment-date-time-step']['fields']['time'] }}</td>
                    </tr>
                  </table>
                  <div role="separator" style="background-color: #e2e8f0; height: 1px; line-height: 1px; `background-color: $color;">&zwj;</div>
                  <div role="separator" style="line-height: 40px; `mso-line-height-alt: ${msoHeight}` }}">&zwj;</div>
                  <p style="margin: 0;">Let op: De afspraak staat nog niet vast. Zodra deze is bevestigd, krijg je hiervan bericht.
                    <br>
                    <br>
                    Met vriendelijke groet, <br>
                    Sanne Terpstra
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