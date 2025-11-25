@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Activity Log Settings</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.activity-log.index') }}">Activity Logs</a></div>
            <div class="breadcrumb-item active">Settings</div>
        </div>
    </div>
    
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Activity Log Configuration</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.activity-log.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="activity_log_whois_enabled" 
                                           name="activity_log_whois_enabled"
                                           {{ (isset($settings['activity_log_whois_enabled']) && $settings['activity_log_whois_enabled'] == '1') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="activity_log_whois_enabled">
                                        <strong>Enable Whois Functionality (IP Geolocation)</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    When enabled, the system will perform IP geolocation lookups to determine user location (country and city) for activity logs. 
                                    This requires external API calls to ip-api.com. Disable this if you want to reduce API calls or improve performance.
                                </small>
                            </div>
                            
                            <hr>
                            
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="activity_log_track_admin" 
                                           name="activity_log_track_admin"
                                           {{ (!isset($settings['activity_log_track_admin']) || $settings['activity_log_track_admin'] == '1') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="activity_log_track_admin">
                                        <strong>Track Admin Users</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    When enabled, admin user activities (create, update, delete, approve operations) will be tracked in activity logs. 
                                    This is typically enabled to maintain an audit trail of administrative actions.
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="activity_log_track_frontend" 
                                           name="activity_log_track_frontend"
                                           {{ (!isset($settings['activity_log_track_frontend']) || $settings['activity_log_track_frontend'] == '1') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="activity_log_track_frontend">
                                        <strong>Track Frontend Users</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    When enabled, frontend user activities (viewing, commenting, exporting) will be tracked in activity logs. 
                                    This helps monitor user engagement and behavior on the website.
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Settings
                                </button>
                                <a href="{{ route('admin.activity-log.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Activity Logs
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Settings Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> About Activity Log Settings</h5>
                            <ul class="mb-0">
                                <li><strong>Whois Functionality:</strong> Enables IP geolocation lookups for activity logs. When disabled, location data will not be collected, but other activity log data will still be tracked.</li>
                                <li><strong>Track Admin Users:</strong> Controls whether admin panel activities are logged. This should typically be enabled for security and audit purposes.</li>
                                <li><strong>Track Frontend Users:</strong> Controls whether public website user activities (viewing, commenting, exporting) are logged. Enable this to monitor user engagement.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


