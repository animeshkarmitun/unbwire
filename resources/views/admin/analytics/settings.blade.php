@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Analytics Settings</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></div>
            <div class="breadcrumb-item active">Settings</div>
        </div>
    </div>
    
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Analytics Configuration</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.analytics.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="analytics_whois_enabled" 
                                           name="analytics_whois_enabled"
                                           {{ (isset($settings['analytics_whois_enabled']) && $settings['analytics_whois_enabled'] == '1') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="analytics_whois_enabled">
                                        <strong>Enable Whois Functionality (IP Geolocation)</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    When enabled, the system will perform IP geolocation lookups to determine visitor country and city. 
                                    This requires external API calls to ip-api.com. Disable this if you want to reduce API calls or improve performance.
                                </small>
                            </div>
                            
                            <hr>
                            
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="analytics_track_admin" 
                                           name="analytics_track_admin"
                                           {{ (isset($settings['analytics_track_admin']) && $settings['analytics_track_admin'] == '1') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="analytics_track_admin">
                                        <strong>Track Admin Users</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    When enabled, admin user activities (visits to admin panel pages) will be tracked in analytics. 
                                    Disable this to exclude admin traffic from your analytics reports.
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="analytics_track_frontend" 
                                           name="analytics_track_frontend"
                                           {{ (!isset($settings['analytics_track_frontend']) || $settings['analytics_track_frontend'] == '1') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="analytics_track_frontend">
                                        <strong>Track Frontend Users</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    When enabled, frontend user activities (visits to public website pages) will be tracked in analytics. 
                                    This is typically enabled to track your actual website visitors.
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Settings
                                </button>
                                <a href="{{ route('admin.analytics.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Analytics
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
                            <h5><i class="fas fa-info-circle"></i> About Analytics Settings</h5>
                            <ul class="mb-0">
                                <li><strong>Whois Functionality:</strong> Enables IP geolocation lookups. When disabled, country and city data will not be collected, but other analytics data will still be tracked.</li>
                                <li><strong>Track Admin Users:</strong> Controls whether admin panel visits are tracked. Disabling this helps keep your analytics focused on actual website visitors.</li>
                                <li><strong>Track Frontend Users:</strong> Controls whether public website visits are tracked. This should typically be enabled to track your actual website traffic.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


