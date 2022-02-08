<div class="flex-container{if="$auth_level >= $auth_level_administrator"}-7{/if}">
    {if="$auth_level >= $auth_level_administrator"}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-balance-scale-right"></i> Auths</h5>
            <p class="card-text">Manage authentications permitted by tickets and used in tracks and segments.</p>
        </div>
        <a href="{$_base_}backend/news" class="btn btn-primary">Go there</a>
    </div>
    {/if}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-newspaper"></i> News</h5>
            <p class="card-text">Manage news accessible to all users of the platform.</p>
        </div>
        <a href="{$_base_}backend/news" class="btn btn-primary">Go there</a>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="far fa-bell"></i> Notifications</h5>
            <p class="card-text">Create and update notifications that registered users will get.</p>
        </div>
        <a href="{$_base_}backend/notifications" class="btn btn-primary">Go there</a>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-user-edit"></i> Papers</h5>
            <p class="card-text">Manage information about papers and their authors.</p>
        </div>
        <a href="{$_base_}backend/papers" class="btn btn-primary">Go there</a>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="far fa-calendar-alt"></i> Schedule Segments</h5>
            <p class="card-text">Manage segments of the schedule.</p>
        </div>
        <a href="{$_base_}backend/segments" class="btn btn-primary">Go there</a>
    </div>
    {if="$auth_level >= $auth_level_administrator"}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-ticket-alt"></i> Tickets</h5>
            <p class="card-text">Manage tracks for the schedule.</p>
        </div>
        <a href="{$_base_}backend/tracks" class="btn btn-primary">Go there</a>
    </div>
    {/if}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-clock"></i> Timeslots</h5>
            <p class="card-text">Manage timeslots for the schedule.</p>
        </div>
        <a href="{$_base_}backend/timeslots" class="btn btn-primary">Go there</a>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-ellipsis-v"></i> Tracks</h5>
            <p class="card-text">Manage tracks for the schedule.</p>
        </div>
        <a href="{$_base_}backend/tracks" class="btn btn-primary">Go there</a>
    </div>
    {if="$auth_level >= $auth_level_administrator"}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-users"></i> Users</h5>
            <p class="card-text">Manage user accounts.</p>
        </div>
        <a href="{$_base_}backend/users" class="btn btn-primary">Go there</a>
    </div>
    {/if}
</div>