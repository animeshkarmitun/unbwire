<div class="card border border-primary">
    <div class="card-body">
        <form action="{{ route('admin.email-setting.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Configure your email server settings. These settings will be used for all emails sent from the system.
            </div>

            <div class="form-group">
                <label for="mail_mailer"><strong>Mailer Type</strong></label>
                <select name="mail_mailer" id="mail_mailer" class="form-control">
                    <option value="smtp" {{ ($settings['mail_mailer'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                    <option value="sendmail" {{ ($settings['mail_mailer'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                    <option value="mailgun" {{ ($settings['mail_mailer'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                    <option value="ses" {{ ($settings['mail_mailer'] ?? '') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                    <option value="postmark" {{ ($settings['mail_mailer'] ?? '') == 'postmark' ? 'selected' : '' }}>Postmark</option>
                    <option value="log" {{ ($settings['mail_mailer'] ?? '') == 'log' ? 'selected' : '' }}>Log (Testing)</option>
                </select>
                <small class="form-text text-muted">Select the mailer type you want to use.</small>
                @error('mail_mailer')
                <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div id="smtp-settings">
                <h5 class="mt-4 mb-3">SMTP Configuration</h5>

                <div class="form-group">
                    <label for="mail_host"><strong>SMTP Host</strong></label>
                    <input type="text" name="mail_host" id="mail_host" class="form-control" 
                           value="{{ $settings['mail_host'] ?? '' }}" 
                           placeholder="smtp.gmail.com">
                    <small class="form-text text-muted">Your SMTP server hostname.</small>
                    @error('mail_host')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mail_port"><strong>SMTP Port</strong></label>
                    <input type="number" name="mail_port" id="mail_port" class="form-control" 
                           value="{{ $settings['mail_port'] ?? '587' }}" 
                           placeholder="587">
                    <small class="form-text text-muted">Common ports: 587 (TLS), 465 (SSL), 25 (No encryption).</small>
                    @error('mail_port')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mail_encryption"><strong>Encryption</strong></label>
                    <select name="mail_encryption" id="mail_encryption" class="form-control">
                        <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="" {{ ($settings['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>None</option>
                    </select>
                    <small class="form-text text-muted">Encryption method for SMTP connection.</small>
                    @error('mail_encryption')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mail_username"><strong>SMTP Username</strong></label>
                    <input type="text" name="mail_username" id="mail_username" class="form-control" 
                           value="{{ $settings['mail_username'] ?? '' }}" 
                           placeholder="your-email@gmail.com">
                    <small class="form-text text-muted">Your SMTP username (usually your email address).</small>
                    @error('mail_username')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mail_password"><strong>SMTP Password</strong></label>
                    <input type="password" name="mail_password" id="mail_password" class="form-control" 
                           value="{{ $settings['mail_password'] ?? '' }}" 
                           placeholder="Your SMTP password or app password">
                    <small class="form-text text-muted">
                        Your SMTP password. For Gmail, use an App Password instead of your regular password.
                        <br><strong>Note:</strong> Leave blank to keep current password unchanged.
                    </small>
                    @error('mail_password')
                    <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <h5 class="mt-4 mb-3">From Address Configuration</h5>

            <div class="form-group">
                <label for="mail_from_address"><strong>From Email Address</strong></label>
                <input type="email" name="mail_from_address" id="mail_from_address" class="form-control" 
                       value="{{ $settings['mail_from_address'] ?? config('mail.from.address') }}" 
                       placeholder="noreply@example.com">
                <small class="form-text text-muted">The email address that will appear as the sender.</small>
                @error('mail_from_address')
                <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="mail_from_name"><strong>From Name</strong></label>
                <input type="text" name="mail_from_name" id="mail_from_name" class="form-control" 
                       value="{{ $settings['mail_from_name'] ?? getSetting('site_name') ?? 'UNB News' }}" 
                       placeholder="UNB News">
                <small class="form-text text-muted">The name that will appear as the sender.</small>
                @error('mail_from_name')
                <p class="text-danger">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Email Settings
                </button>
                <button type="button" class="btn btn-info" id="test-email-btn">
                    <i class="fas fa-paper-plane"></i> Test Email Configuration
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Show/hide SMTP settings based on mailer type
        function toggleSmtpSettings() {
            const mailer = $('#mail_mailer').val();
            if (mailer === 'smtp') {
                $('#smtp-settings').show();
            } else {
                $('#smtp-settings').hide();
            }
        }

        $('#mail_mailer').on('change', toggleSmtpSettings);
        toggleSmtpSettings(); // Initial call

        // Test email configuration
        $('#test-email-btn').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            
            // Validate required fields
            if ($('#mail_mailer').val() === 'smtp') {
                if (!$('#mail_host').val() || !$('#mail_username').val()) {
                    alert('Please fill in SMTP Host and SMTP Username before testing.');
                    return;
                }
            }
            
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
            
            $.ajax({
                url: '{{ route("admin.email-setting.test") }}',
                method: 'POST',
                timeout: 30000, // 30 second timeout
                data: {
                    _token: '{{ csrf_token() }}',
                    mail_mailer: $('#mail_mailer').val(),
                    mail_host: $('#mail_host').val(),
                    mail_port: $('#mail_port').val(),
                    mail_encryption: $('#mail_encryption').val(),
                    mail_username: $('#mail_username').val(),
                    mail_password: $('#mail_password').val(),
                    mail_from_address: $('#mail_from_address').val(),
                    mail_from_name: $('#mail_from_name').val(),
                },
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Success: ' + (response.message || 'Test email sent successfully! Please check your inbox.'));
                    } else {
                        alert('Error: ' + (response.message || 'Failed to send test email.'));
                    }
                },
                error: function(xhr) {
                    let message = 'Failed to send test email. ';
                    
                    if (xhr.status === 0) {
                        message += 'Connection timeout. Please check your SMTP settings and try again.';
                    } else if (xhr.status === 500) {
                        message += xhr.responseJSON?.message || 'Server error occurred.';
                    } else if (xhr.status === 422) {
                        message += 'Validation error: ' + (xhr.responseJSON?.message || 'Please check all required fields.');
                    } else {
                        message += xhr.responseJSON?.message || 'Please check your configuration.';
                    }
                    
                    alert('Error: ' + message);
                    console.error('Email test error:', xhr);
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
@endpush
