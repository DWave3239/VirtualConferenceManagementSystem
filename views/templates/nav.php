<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="{$_base_}main/index">Conference <small class="text-muted">Mockup</small>&nbsp;<img src="{$assets_url}/images/og_image.jpg" height="25"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="{$_base_}main/index">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="modal" data-target="#schedule2">Schedule</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{$_base_}main/news">News</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{$_base_}main/committees">Committees</a>
        </li>
        {if="$logged_in"}
          <li class="nav-item">
            <a class="nav-link" href="{$_base_}participation/index">Participation Hub</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="https://gather.town/invite?token=VSr3bs2EwDIv2BSiY1EHfOCfbEfgKTYo" target="_blank">Town Hall</a>
          </li>
          {if="$auth_level >= $auth_level_program_chair"}
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="{$_base_}backend/index" id="backendMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Backend
              </a>
              <div class="dropdown-menu" aria-labelledby="backendMenuLink">
                <a class="dropdown-item" href="{$_base_}backend/news"><i class="fas fa-newspaper navBarIconWidth"></i> News</a>
                <a class="dropdown-item" href="{$_base_}backend/notifications"><i class="far fa-bell navBarIconWidth"></i> Notification</a>
                <a class="dropdown-item" href="{$_base_}backend/papers"><i class="fas fa-user-edit navBarIconWidth"></i> Papers</a>
                <a class="dropdown-item" href="{$_base_}backend/segments"><i class="far fa-calendar-alt navBarIconWidth"></i> Schedule Segments</a>
                <a class="dropdown-item" href="{$_base_}backend/timeslots"><i class="fas fa-clock navBarIconWidth"></i> Timeslots</a>
                <a class="dropdown-item" href="{$_base_}backend/tracks"><i class="fas fa-ellipsis-v navBarIconWidth"></i> Tracks</a>
                {if="$auth_level == $auth_level_administrator"}
                <a class="dropdown-item" href="{$_base_}backend/auths"><i class="fas fa-balance-scale-right"></i> Auths</a>
                <a class="dropdown-item" href="{$_base_}backend/tickets"><i class="fas fa-ticket-alt"></i> Tickets</a>
                <a class="dropdown-item" href="{$_base_}backend/users"><i class="fas fa-users navBarIconWidth"></i> Users</a>
                <a class="dropdown-item" href="{$_base_}statistics/index"><i class="fas fa-chart-bar navBarIconWidth"></i> Statistics</a>
                {/if}
              </div>
            </li>
          {/if}
        {else}
          <li class="nav-item">
            <a class="nav-link" href="{$_base_}main/register">Registration</a>
          </li>
        {/if}
      </ul>
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          {if="$logged_in"}
            <a class="nav-link" href="{$_base_}main/logout">Logout</a>
          {else}
            <a class="nav-link" data-toggle="modal" data-target="#loginForm"><button class="btn btn-primary" style="padding: 3px 10px">Login</button></a>
          {/if}
        </li>
      </ul>
      <ul class="navbar-nav justify-content-end">
        {if="$logged_in"}
          <li>
            <a class="nav-link" data-toggle="modal" data-target="#latestNotifications" onclick="loadNotifications('latestNotifications')">
              <i class="far fa-bell"></i>
            </a>
          </li>
        {/if}
        {if="isset($debug) && $debug"}
          <li><a class="nav-link" data-toggle="modal" data-target="#debug">Debug</a></li>
          <li><a class="nav-link" href="{$_base_}main/session_killer">RESET</a></li>
        {/if}
      </ul>
    </div>
  </nav>
  {if="isset($subnav)"}
    {include="subnavs/$subnav"}
  {/if}
  