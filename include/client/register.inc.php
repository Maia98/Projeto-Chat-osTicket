<?php

    $info = $_POST;
    if (!isset($info['timezone_id']))
        $info += array(
            'timezone_id' => $cfg->getDefaultTimezoneId(),
            'dst' => $cfg->observeDaylightSaving(),
            'backend' => null,
        );
    if (isset($user) && $user instanceof ClientCreateRequest) {
        $bk = $user->getBackend();
        $info = array_merge($info, array(
            'backend' => $bk::$id,
            'username' => $user->getUsername(),
        ));
    }
    $info = Format::htmlchars(($errors && $_POST)?$_POST:$info);

?>
<h1>
    <?php echo __('Account Registration'); ?>
</h1>
<p>
    <?php echo __('Use the forms below to create or update the information we have on file for your account'); ?>
</p>
<form action="account.php" method="post">
  <?php csrf_token(); ?>
  <input type="hidden" name="do" value="<?php echo Format::htmlchars($_REQUEST['do'] ?: ($info['backend'] ? 'import' :'create')); ?>" />
  <div class="col-md-12" style="margin-bottom: 75px; ">
      <div class="row">
          <?php
              $cf = $user_form ?: UserForm::getInstance();
              $cf->render(false);
          ?>
      </div>
      <div class="row">
          <div>
              <hr>
              <h3><?php echo __('Preferences'); ?></h3>
          </div>
          <div class="row">
              <div class="col-md-7">
                  <label><?php echo __('Time Zone'); ?>:</label>
                      <select class="form-control" name="timezone_id" id="timezone_id">
                          <?php
                              $sql='SELECT id, offset,timezone FROM '.TIMEZONE_TABLE.' ORDER BY id';
                              if(($res=db_query($sql)) && db_num_rows($res)){
                                  while(list($id,$offset, $tz)=db_fetch_row($res)){
                                      $sel=($info['timezone_id']==$id)?'selected="selected"':'';
                                      echo sprintf('<option value="%d" %s>GMT %s - %s</option>',$id,$sel,$offset,$tz);
                                  }
                              }
                          ?>
                      </select>
                      &nbsp;<span class="error"><?php echo $errors['timezone_id']; ?></span>
              </div>
          </div>
          <div class="row">
            <div class="col-md-7">
                <label><?php echo __('Daylight Saving'); ?>:</label>
                <input type="checkbox" name="dst" value="1" <?php echo $info['dst']?'checked="checked"':''; ?> />
                <?php echo __('Observe daylight saving'); ?>
                <em>(<?php echo __('Current Time'); ?>:
                    <strong>
                      <?php echo Format::date($cfg->getDateTimeFormat(),Misc::gmtime(),$info['tz_offset'],$info['dst']); ?>
                    </strong>)
                </em>
            </div>
          </div>
      </div>
      <div class="row">
          <div>
              <hr>
              <h3><?php echo __('Access Credentials'); ?></h3>
              <?php if ($info['backend']) { ?>
                  <div class="col-md-5">
                      <label>
                          <?php echo __('Login With'); ?>:
                      </label>
                      <div>
                          <input class="form-control" type="hidden" name="backend" value="<?php echo $info['backend']; ?>"/>
                          <input class="form-control" type="hidden" name="username" value="<?php echo $info['username']; ?>"/>
                          <?php foreach (UserAuthenticationBackend::allRegistered() as $bk) {
                              if ($bk::$id == $info['backend']) {
                                  echo $bk->getName();
                                  break;
                              }
                          } ?>
                      </div>
                  </div>
              <?php } else { ?>
                  <div class="col-md-5">
                      <label><?php echo __('Create a Password'); ?>: </label>
                      <div>
                          <input class="form-control" type="password" name="passwd1" value="<?php echo $info['passwd1']; ?>">

                      </div>
                      &nbsp;<span class="error">&nbsp;<?php echo $errors['passwd1']; ?></span>
                  </div>
                  <div class="col-md-5">
                      <label><?php echo __('Confirm New Password'); ?>: </label>
                      <div>
                          <input class="form-control" type="password" size="18" name="passwd2" value="<?php echo $info['passwd2']; ?>">
                          &nbsp;<span class="error">&nbsp;<?php echo $errors['passwd2']; ?></span>
                      </div>
                  </div>

              <?php } ?>
          </div>
      </div>
      <div class="row">
          <hr>
          <div style="text-align: center;">
              <input class="btn btn-success" type="submit" value="Register"/>
              <input class="btn btn-danger" type="button" value="Cancel" onclick="javascript:window.location.href='index.php';"/>
          </div>
      </div>
  </div>
</form>
<div style="margin-bottom: 100px;"></div>
