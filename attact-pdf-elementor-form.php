add_action('elementor_pro/forms/new_record', function($record, $handler) {

    // Match your exact form name from Elementor
    $form_name = $record->get_form_settings('form_name');
    if ( 'Info Form' !== $form_name ) {
        return;
    }

    // Get form fields
    $raw_fields = $record->get('fields');
    $fields = [];
    foreach ($raw_fields as $id => $field) {
        $fields[$id] = $field['value'];
    }

    // CHANGE 'email' IF your email field ID is different
    if (empty($fields['email'])) {
        return; // no email field, stop
    }

    $customer_email = $fields['email'];
    $name = isset($fields['name']) ? $fields['name'] : '';

    // Build something similar to [all-fields]
    $all_fields_html = '';
    foreach ($fields as $id => $value) {
        // Skip internal fields if you want (optional)
        if (in_array($id, ['submit', 'acceptance'], true)) {
            continue;
        }
        $label = ucwords(str_replace(['-', '_'], ' ', $id));
        $all_fields_html .= '<p><strong>' . esc_html($label) . ':</strong> ' . nl2br(esc_html($value)) . '</p>';
    }

    // Get uploads base dir and build PDF path
    $upload_dir = wp_upload_dir();
    $attachment_path = $upload_dir['basedir'] . '/2025/11/Frontier-Lesson-Program-2025.pdf';

    if (!file_exists($attachment_path)) {
        // If the file is not found, don't try to send attachment
        return;
    }

    // Subject
    $subject = 'Thank you – Frontier Ski & Snow School (Program PDF Attached)';

    // HTML email body – your template with dynamic name + all-fields
    $message = <<<HTML
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="font-family: Arial, Helvetica, 'Helvetica Neue', Helvetica, sans-serif; background:#f6f7f9; padding:24px 0;">
  <tr>
    <td align="center">
      <table width="680" cellpadding="0" cellspacing="0" role="presentation" style="max-width:680px; width:100%; background:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 8px 30px rgba(23,26,34,0.08);">
        <!-- Header -->
        <tr>
          <td style="background:#e62029; padding:28px 30px; color:#FFF8E8; text-align:left;">
            <h1 style="margin:0; font-size:20px; line-height:1.1; font-weight:700; letter-spacing:0.2px;">
              Thank you, <span style="color:#ffffff;">{$name}</span>
            </h1>
            <p style="margin:6px 0 0; font-size:13px; color: #FFF8E8; opacity:0.95;">
              We received your submission and will contact you very soon.
            </p>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:24px 30px; color:#121212; font-size:15px; line-height:1.6;">
            <p style="margin:0 0 14px;">Hello <strong>{$name}</strong>,</p>

            <p style="margin:0 0 14px;">
              Thank you for providing your information. We have collected all the details you shared below — our team will review them and get in touch with you shortly.
            </p>

            <h3 style="margin:18px 0 8px; font-size:16px; color:#e62029;">Your submitted information</h3>

            <!-- Info box -->
            <div style="background:#fbfbfb; border:1px solid #eee; padding:14px; border-radius:6px; font-size:14px; color:#333;">
              {$all_fields_html}
            </div>

            <p style="margin:18px 0 0; font-size:14px; color:#666;">
              We have also attached the <strong>Frontier Lesson Program 2025</strong> as a PDF to this email for your reference.
            </p>

            <p style="margin:18px 0 0; font-size:14px; color:#666;">
              If any of the details above are incorrect, reply to this email or contact us at
              <a href="mailto:info@frontierhakuba.com" style="color:#e62029; text-decoration:none;">info@frontierhakuba.com</a>.
            </p>
          </td>
        </tr>

        <!-- Footer CTA -->
        <tr>
          <td style="padding:18px 30px 28px;">
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
              <tr>
                <td align="left">
                  <a href="https://frontierhakuba.com/" style="display:inline-block; background:#e62029; color:#FFF8E8; text-decoration:none; padding:12px 18px; border-radius:8px; font-weight:600; font-size:14px;">Visit Our Website</a>
                </td>
                <td align="right" style="font-size:13px; color:#999;">
                  <div style="margin-bottom:6px;">Best regards,</div>
                  <div style="font-weight:700; color:#121212;">Frontier Ski & Snow School</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Small footer -->
        <tr>
          <td style="background:#f2f3f5; padding:12px 30px; font-size:12px; color:#8a8f94; text-align:center;">
            You are receiving this message because you contacted us via our website form.
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
HTML;

    // Headers
    $headers = [
        'From: Frontier Ski & Snow School <info@frontierhakuba.com>',
        'Content-Type: text/html; charset=UTF-8'
    ];

    // Send email with PDF attached
    wp_mail(
        $customer_email,
        $subject,
        $message,
        $headers,
        [$attachment_path]
    );

}, 10, 2);
