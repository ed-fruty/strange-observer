Strange PHP observer (JFF)
================

Sometimes we need to listen class calling actions, but for example we haven't access/permissions to edit it and paste here event fire.

Sometimes we have some instance of class and we work with it and we can't extend this class. (For example Database class. Instance was created and if we try to create new extend instance - new connection will be created. Or class can be final)

But functional of this class does not satisfy to our needs.

For this actions you can use this strange observer ;)

#Installation

Install package via composer
  
  ```bash
  composer require "ed-fruty/strange-observer": "dev-master"
```

#Usage

For example we have class `User` with method `register($attributes)` and we want to add listeners for this method

  ```php
  <?php
  
  use Fruty\Observe\Manager;
  use Fruty\Observe\Event;
  
  class User
  {
    /**
     * @access public
     * @param array $attributes
     * @return bool
     */
    public function register(array $attributes)
    {
      return $this->save($attributes);
    }
  }
  
  // create instance
  $user = Manager::make('User');
  
  // or we can use instance of User extend of class name
  $instance = new User();
  $user = Manager::make($instance);
  
  ```
  All done!
  We can use `$user` as previously
  
  ```php
  $user->register(array('username' => 'root'));
  ```
  Ok, now we want to add validation before register
  
  ```php
  $user()->before('register', function(Event $event)
  {
    // validate data
    // get method arguments
    $params = $event->getParams();
    
    //... some validation logic
    
    // for abort calling register method (if validation is failed) use
    $event->stopPropagation();
    
    // for setting some return value
    $event->setResult("Validation errors");
  });
  ```
  
  And we want to send email when registration was successfull
  
  ```php
  $user()->after('register', function(Event $event)
  {
    if ($event->getResult()) {
      // in $event->getResult() result of executed method, in our case it is boolean and if it true - registration was successfull, so send email
      $email = $event->getParams()[0]['email'];
      $mailer = new Mailer();
      $mailer->body("Congratulations!")
        ->to($email)
        ->send();
    }
  });
  
  ```
  
  Now we can use
  
  ```php
  $user->register(array('username' => 'root', 'email' => 'root@iam.com'));
  ```
  
  We can set priorities to the listeners
  
  ```php
  
  $user()->before('register', function(Event $event)
  {
    // this code will be executed early than validation, because it have higher priority (Event::PRIORITY_HIGH)
  }, Event::PRIORITY_HIGH);
  
  ```
  
  Also, we can bind new methods dinamically to the $user
  
  ```php
  
  $user()->bind('updateLastLoginTime', function($userId)
  {
    //code
  });
  
  $user->updateLastLoginTime(5);
  ```
  
  We can redeclare existing methods
  
  ```php
  
  $user()->bind('register', function(array $attributes, $sendMailNotification = true)
  {
    // call parent method
    $this(true)->register($attributes);
    if ($sendMailNotification) {
      // you can use $this and $this(true)
      $this->sendMailNotification();
    }
  });
  
  $user->register(array('username' => 'root', 'email' => 'root@iam.com'), true);
  ```
  
As can see we in code uses 3 calling methods
  1. `$user->method()`
  2. `$user()->method()`
  3. `$user(true)->method()`
  
   - `$user` is instanece of `Invoker` class and call `$user->method()` remap to the real instance of `User` class and can be listened
   - `$user(true)` is real instance of `User`, calling `$user(true)->method()` can not be listened
   - `$user()` is instance of observer to adding new listeners, binding new methods
So, when you bind method

  ```php
  $user->bind('getOne', function($userId)
  {
    //$this is instance of Invoker, methods will remap to the instance of User and they can be listened
    //$this(true) is instance of User, methods cannot be listened
    
    // NOTE!!!
    // if you write here $this->getOne() script will call this action again and again, so you must to use 
    // $this(true)->getOne() to use real User::getOne()
    
    // if you call here
    $this->register(); // it will be called and listened
    $this(true)->register(); // it will be called but not listened, this is original User::register()
    $this->updateLastLoginTime(); // it will be called and listened
    $this(true)->updateLastLoginTime(); // Fatal error, calling undefined method User::updateLastLoginTime()
    
    return array('some data');
  });
  
  ```
  
  We can add subscriber to the `$user` calls
  
  ```php
  
  class UserSubscriber extends Fruty\Observe\AbstractSubscriber
  {
    /**
     * This method will call before calling User::register()
     *
     * @access public
     * @static 
     * @param Event $event
     */
    public static function onBeforeRegister(Event $event)
    {
      // here code
    }
    
    /**
     * This method will call after calling User::register()
     *
     * @access public
     * @static 
     * @param Event $event
     */
    public static function onAfterRegister(Event $event)
    {
      // here code
    }
    
    /**
     * We can set priorities to the actions
     *
     * @access public
     * @static
     * @return array
     */
     public function getPriorities()
     {
      return array(
        'onBeforeRegister' => Event::PRIORITY_HIGHT,
        'onAfterRegiter' => Event::PRIORITY_LOW,
      );
     }
  }
  
  $user()->subscribe('UserSubscriber');
  
  ```
  
  How to check instance ?
  
  ```php
  
  if ($user instanceof User === true) {
    // Fail. $user is instance of Fruty\Observe\Invoker
  }
  
  if ($user(true) instanceof User) {
    // Success
  }
