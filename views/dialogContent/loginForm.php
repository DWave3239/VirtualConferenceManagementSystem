<form method="POST" action="{$_base_}main/login">
  <div class="form-group">
    <label for="username">Username</label>
    <select class="form-control" name="username" id="username" placeholder="Choose your account">
      {loop="users"}
      <option value="{$value.username}">{$value.full_name}</option>
      {/loop}
    </select>
    <!--<input type="text" class="form-control" id="username" name="username" placeholder="Enter username">-->
  </div>
  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="hello">
    <!--<input type="password" class="form-control" id="password" name="password" placeholder="Password">-->
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>