@extends('admin.layouts.master')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ __('admin.Dashboard') }}</h1>
    </div>

    @if (canAccess(['news index']))
    <div class="mb-3">
        <h6 class="text-primary mb-2">My News Statistics</h6>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total News</h4>
                    </div>
                    <div class="card-body">
                        {{ $userTotalNews }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total News Last 30 Days</h4>
                    </div>
                    <div class="card-body">
                        {{ $userTotalNewsLast30Days }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total News Last 7 Days</h4>
                    </div>
                    <div class="card-body">
                        {{ $userTotalNewsLast7Days }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total News Today</h4>
                    </div>
                    <div class="card-body">
                        {{ $userTotalNewsToday }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
    @endif

    @if (canAccess(['news index']))
    <div class="mb-3 mt-2">
        <h6 class="text-primary mb-2">Overall News Statistics</h6>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Overall Total News</h4>
                    </div>
                    <div class="card-body">
                        {{ $overallTotalNews }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-globe"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Overall News Last 30 Days</h4>
                    </div>
                    <div class="card-body">
                        {{ $overallNewsLast30Days }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-globe-asia"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Overall News Last 7 Days</h4>
                    </div>
                    <div class="card-body">
                        {{ $overallNewsLast7Days }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Overall News Today</h4>
                    </div>
                    <div class="card-body">
                        {{ $overallNewsToday }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @if (canAccess(['ap photo index']))
    <div class="mb-3 mt-2">
        <h6 class="text-primary mb-2">AP Photo Statistics</h6>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-camera"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total AP Photos</h4>
                    </div>
                    <div class="card-body">
                        {{ $apTotalPhotos }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-th-list"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Categories</h4>
                    </div>
                    <div class="card-body">
                        {{ $apTotalCategories }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Tags</h4>
                    </div>
                    <div class="card-body">
                        {{ $apTotalTags }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-history"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Uploads Last 90 Days</h4>
                    </div>
                    <div class="card-body">
                        {{ $apPhotosLast90Days }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Uploads Last 30 Days</h4>
                    </div>
                    <div class="card-body">
                        {{ $apPhotosLast30Days }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Uploads Last 7 Days</h4>
                    </div>
                    <div class="card-body">
                        {{ $apPhotosLast7Days }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Uploads Today</h4>
                    </div>
                    <div class="card-body">
                        {{ $apPhotosToday }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if (canAccess(['access management index']))
    <div class="mb-3 mt-2">
        <h6 class="text-primary mb-2">System Summary</h6>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ __('admin.Total News') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $publishedNews }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                    <i class="far fa-newspaper"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ __('admin.Pending News') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $pendingNews }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="far fa-file"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ __('admin.Total Categories') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $Categories }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-language"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ __('admin.Total Languages') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $languages }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ __('admin.Total Roles') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $roles }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ __('admin.Total Permissions') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $permissions }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                    <i class="fas fa-hashtag"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>{{ __('admin.Total Socials') }}</h4>
                    </div>
                    <div class="card-body">
                        {{ $socials }}
                    </div>
                </div>
            </div>
        </div>

    </div>
    @endif

</section>
@endsection
