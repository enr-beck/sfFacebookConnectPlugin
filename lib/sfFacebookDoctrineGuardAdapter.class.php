<?php

/**
 *
 * @package    sfFacebookConnectPlugin
 * @author     Fabrice Bernhard
 *
 */
class sfFacebookDoctrineGuardAdapter extends sfFacebookGuardAdapter
{
  
   /**
   * Gets the Php name given to the field
   *
   * @param string $field
   * @return string
   * @author fabriceb
   * @since 2009-05-17
   * @since 2009-09-01 added configurability for Doctrine
   */
  public function getProfilePhpName($field_name)
  {
    
    return $this->getFieldName($field_name);
  }
  
  /**
   * Gets the Php name given to the field
   *
   * @param string $field
   * @return string
   * @author fabriceb
   * @since 2009-05-17
   * @since 2009-09-01 added configurability for Doctrine
   */
  public function getProfileColumnName($field_name)
  {
    
    return $this->getFieldName($field_name);
  }
  
  /**
   * Sets a property of the profile of the user
   *
   * @param sfGuardUser $user
   * @param string $property_name
   * @param mixed $property
   */
  public function setUserProfileProperty(&$user, $property_name, $property)
  {
    $user->getProfile()->$property_name = $property;
  }

  /**
   * Gets a property of the profile of the user
   *
   * @param sfGuardUser $user
   * @param string $property_name
   * @return mixed
   * @author fabriceb
   * @since 2009-05-17
   */
  public function getUserProfileProperty($user, $property_name)
  {
    return $user->getProfile()->$property_name;
  }
  
  /**
   * gets a sfGuardUser using the facebook_uid column of his Profile class  
   *
   * @param Integer $facebook_uid
   * @return sfGuardUser
   * @author fabriceb
   * @since 2009-05-17
   */
  public function getSfGuardUserByFacebookUid($facebook_uid)
  {
    $q = Doctrine_Query::create()
      ->from('sfGuardUser u')
      ->innerJoin('u.Profile p')
      ->where('p.'.$this->getFacebookUidColumn().' = ?', $facebook_uid);
    
    if ($q->count())
    {
      
      return $q->fetchOne();
    } 
  
    return null;
  }
  
  /**
   * tries to get a sfGuardUser using the facebook email hash  
   *
   * @param string[] $email_hashes
   * @return sfGuardUser
   * @author fabriceb
   * @since 2009-05-17
   */
  public function getSfGuardUserByEmailHashes($email_hashes)
  {
    if (!is_array($email_hashes) || count($email_hashes) == 0)
    {
      
      return null;
    }
    
    $q = Doctrine_Query::create()
      ->from('sfGuardUser u')
      ->innerJoin('u.Profile p')
      ->whereIn('p.'.$this->getEmailHashColumn(), $email_hashes);
    
    if ($q->count())
    {
      // NOTE: if a user has multiple emails on their facebook account,
      // and more than one is registered on the site, then we will
      // only return the first one.
    
      return $q->fetchOne();
    } 
  
    return null;
  }
  
  /**
   * Creates an empty sfGuardUser with profile field Facebook UID set
   *
   * @param Integer $facebook_uid
   * @return sfGuardUser
   * @author fabriceb
   * @since 2009-08-11
   */
  public function createSfGuardUserWithFacebookUid($facebook_uid)
  {
    $con = Doctrine::getConnectionByTableName('sfGuardUser');
    
    return parent::createSfGuardUserWithFacebookUidAndCon($facebook_uid, $con);
  }
  
  /**
   * gets Non Facebook-registered Users
   *
   * @return sfGuardUser[]
   * @author fabriceb
   * @since 2009-05-17
   */
  public function getNonRegisteredUsers()
  {
    $q = Doctrine_Query::create()
      ->from('sfGuardUser u')
      ->innerJoin('u.Profile p')
      ->where('p.'.$this->getEmailHashColumn().' IS NULL');
    
    return $q->execute();
  }  
  
  /**
  *
  * @param string $cookie
  * @return sfGuardUser
  * @author fabriceb
  * @since Aug 10, 2009
  */
  public function retrieveSfGuardUserByCookie($cookie)
  {
    $q = Doctrine_Query::create()
      ->from('sfGuardRememberKey r')
      ->innerJoin('r.sfGuardUser u')
      ->where('r.remember_key = ?', $cookie);
    
    if ($q->count())
    {
      
      return $q->fetchOne()->sfGuardUser;
    } 
  
    return null;
  }
}
  
