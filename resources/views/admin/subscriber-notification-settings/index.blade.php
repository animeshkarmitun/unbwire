@extends('admin.layouts.master')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Notification & Email Settings</h1>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h4>Subscriber Notification Settings</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subscriber-notification-settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="notification_email_enabled" 
                                   name="notification_email_enabled" value="1"
                                   {{ ($settings['notification_email_enabled'] ?? '1') == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="notification_email_enabled">
                                <strong>Enable Email Notifications</strong>
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            When enabled, subscribers will receive email notifications when news is published.
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="notification_send_to_all" 
                                   name="notification_send_to_all" value="1"
                                   {{ ($settings['notification_send_to_all'] ?? '1') == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="notification_send_to_all">
                                <strong>Send to All Eligible Subscribers</strong>
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            When enabled, notifications are sent to all subscribers who can access the news based on their subscription tier.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="notification_email_rate_limit">
                            <strong>Email Rate Limit (per minute)</strong>
                        </label>
                        <input type="number" class="form-control" id="notification_email_rate_limit" 
                               name="notification_email_rate_limit" 
                               value="{{ $settings['notification_email_rate_limit'] ?? '100' }}"
                               min="1" max="1000">
                        <small class="form-text text-muted">
                            Maximum number of emails to send per minute. Recommended: 100-200.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="notification_batch_size">
                            <strong>Batch Size</strong>
                        </label>
                        <input type="number" class="form-control" id="notification_batch_size" 
                               name="notification_batch_size" 
                               value="{{ $settings['notification_batch_size'] ?? '100' }}"
                               min="10" max="500">
                        <small class="form-text text-muted">
                            Number of subscribers to process in each batch. Recommended: 100-200.
                        </small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Email Template Configuration -->
        <div class="card card-success">
            <div class="card-header">
                <h4>Email Template Configuration</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subscriber-notification-settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Configure what content is included in the email when news is published. 
                        Full news content will be sent based on subscriber's subscription tier access.
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="email_template_send_full_content" 
                                   name="email_template_send_full_content" value="1"
                                   {{ ($settings['email_template_send_full_content'] ?? '1') == '1' ? 'checked' : '' }}>
                            <label class="custom-control-label" for="email_template_send_full_content">
                                <strong>Send Full News Content</strong>
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            When enabled, the complete news article content will be included in the email (based on subscription access).
                        </small>
                    </div>

                    <hr>

                    <h5 class="mb-3">Include in Email:</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_template_include_title" 
                                           name="email_template_include_title" value="1"
                                           {{ ($settings['email_template_include_title'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="email_template_include_title">
                                        News Title
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_template_include_image" 
                                           name="email_template_include_image" value="1"
                                           {{ ($settings['email_template_include_image'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="email_template_include_image">
                                        Featured Image
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_template_include_content" 
                                           name="email_template_include_content" value="1"
                                           {{ ($settings['email_template_include_content'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="email_template_include_content">
                                        Full Content
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_template_include_excerpt" 
                                           name="email_template_include_excerpt" value="1"
                                           {{ ($settings['email_template_include_excerpt'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="email_template_include_excerpt">
                                        Excerpt (if full content not accessible)
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_template_include_author" 
                                           name="email_template_include_author" value="1"
                                           {{ ($settings['email_template_include_author'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="email_template_include_author">
                                        Author Name
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_template_include_category" 
                                           name="email_template_include_category" value="1"
                                           {{ ($settings['email_template_include_category'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="email_template_include_category">
                                        Category
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_template_include_publish_date" 
                                           name="email_template_include_publish_date" value="1"
                                           {{ ($settings['email_template_include_publish_date'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="email_template_include_publish_date">
                                        Publish Date
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_template_include_video_link" 
                                           name="email_template_include_video_link" value="1"
                                           {{ ($settings['email_template_include_video_link'] ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="email_template_include_video_link">
                                        Video Link
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="email_template_include_tags" 
                                           name="email_template_include_tags" value="1"
                                           {{ ($settings['email_template_include_tags'] ?? '0') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="email_template_include_tags">
                                        Tags
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Template Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card-info">
            <div class="card-header">
                <h4>Notification Information</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> How It Works:</h5>
                    <ul>
                        <li>When news is published (status=1 and is_approved=1), notifications are automatically sent to eligible subscribers.</li>
                        <li>Subscribers only receive notifications for news they can access based on their subscription tier.</li>
                        <li>Free tier subscribers (no user account) receive only free news notifications.</li>
                        <li>Subscribers with user accounts receive notifications based on their active subscription package.</li>
                        <li>Email content is filtered based on subscription tier (images, videos, exclusive content).</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
