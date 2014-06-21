<?php
class top
{
    protected $data;
    protected $server;

    public $objectClass;
    public $dn;

    function __construct($server, $data)
    {
        $this->server = $server;
        $this->data = $data;
        $this->dn = $data["dn"];
        $this->objectClass = $data["objectclass"];
    }

    function resetServer($server)
    {
        $this->server = $server;
    }
}

class person extends top
{
    public $sn;
    public $cn;
    public $userPassword;
    public $telephoneNumber;
    public $seeAlso;
    public $description;

    function __construct($server, $data)
    {
        parent::__construct($server, $data);
        $this->sn = $data["sn"];
        $this->cn = $data["cn"];
        /*MAY fields, check for NULL*/
        if(isset($data["userpassword"])) $this->userPassword = $data["userpassword"];
        if(isset($data["telephonenumber"])) $this->telephoneNumber = $data["telephonenumber"];
        if(isset($data["seealso"])) $this->seeAlso = $data["seealso"];
        if(isset($data["description"])) $this->description = $data["description"];
    }
}

class groupofnames extends top
{
    public $member;
    public $cn;
    public $businessCategory;
    public $seeAlso;
    public $owner;
    public $ou;
    public $o;
    public $description;

    function __construct($server, $data)
    {
        parent::__construct($server, $data);
        $this->member = $data["member"];
        $this->cn = $data["cn"];
        /*MAY fields, check for NULL*/
        if(isset($data["businesscategory"])) $this->businessCategory = $data["businesscategory"];
        if(isset($data["seealso"])) $this->seeAlso = $data["seealso"];
        if(isset($data["owner"])) $this->owner = $data["owner"];
        if(isset($data["ou"])) $this->ou = $data["ou"];
        if(isset($data["o"])) $this->o = $data["o"];
        if(isset($data["description"])) $this->description = $data["description"];
    }
}

class organizationalPerson extends person
{
    public $title;
    public $x121Address;
    public $registeredAddress;
    public $destinationIndicator;
    public $preferredDeliveryMethod; 
    public $telexNumber;
    public $teletexTerminalIdentifier;
    public $telephoneNumber;
    public $internationaliSDNNumber;
    public $facsimileTelephoneNumber; 
    public $street; 
    public $postOfficeBox; 
    public $postalCode; 
    public $postalAddress; 
    public $physicalDeliveryOfficeName;
    public $ou;
    public $st;
    public $l;

    function __construct($server, $data)
    {
        parent::__construct($server, $data);
        /*MAY fields, check for NULL*/
        if(isset($data["title"])) $this->title = $data["title"];
        if(isset($data["x121address"])) $this->x121Address = $data["x121address"];
        if(isset($data["registeredaddress"])) $this->registeredAddress = $data["registeredaddress"];
        if(isset($data["destinationindicator"])) $this->destinationIndicator = $data["destinationindicator"];
        if(isset($data["preferreddeliverymethod"])) $this->preferredDeliveryMethod = $data["preferreddeliverymethod"];
        if(isset($data["telexnumber"])) $this->telexNumber = $data["telexnumber"];
        if(isset($data["teletexterminalidentifier"])) $this->teletexTerminalIdentifier = $data["teletexterminalidentifier"];
        if(isset($data["telephonenumber"])) $this->telephoneNumber = $data["telephonenumber"];
        if(isset($data["internationalisdnnumber"])) $this->internationaliSDNNumber = $data["internationalisdnnumber"];
        if(isset($data["facsimiletelephonenumber"])) $this->facsimileTelephoneNumber = $data["facsimiletelephonenumber"];
        if(isset($data["street"])) $this->street = $data["street"];
        if(isset($data["postofficebox"])) $this->postOfficeBox = $data["postofficebox"];
        if(isset($data["postalcode"])) $this->postalCode = $data["postalcode"];
        if(isset($data["postaladdress"])) $this->postalAddress = $data["postaladdress"];
        if(isset($data["physicaldeliveryofficename"])) $this->physicalDeliveryOfficeName = $data["physicaldeliveryofficename"];
        if(isset($data["ou"])) $this->ou = $data["ou"];
        if(isset($data["st"])) $this->st = $data["st"];
        if(isset($data["l"])) $this->l = $data["l"];
    }
}

class inetOrgPerson extends organizationalPerson
{
    public $audio;
    public $businessCategory;
    public $carLicense;
    public $departmentNumber;
    public $displayName;
    public $employeeNumber;
    public $employeeType;
    public $givenName;
    public $homePhone; 
    public $homePostalAddress;
    public $initials;
    public $jpegPhoto;
    public $labeledURI; 
    public $mail; 
    public $manager;
    public $mobile; 
    public $o;
    public $pager;
    public $photo;
    public $roomNumber;
    public $secretary;
    public $uid;
    public $userCertificate;
    public $x500uniqueIdentifier;
    public $preferredLanguage;
    public $userSMIMECertificate;
    public $userPKCS12;

    function __construct($server, $data)
    {
        parent::__construct($server, $data);
        /*MAY fields, check for NULL*/
        if(isset($data["audio"])) $this->audio = $data["audio"];
        if(isset($data["businesscategory"])) $this->businessCategory = $data["businesscategory"];
        if(isset($data["carlicense"])) $this->carLicense = $data["carlicense"];
        if(isset($data["departmentnumber"])) $this->departmentNumber = $data["departmentnumber"];
        if(isset($data["displayname"])) $this->displayName = $data["displayname"];
        if(isset($data["employeenumber"])) $this->employeeNumber = $data["employeenumber"];
        if(isset($data["employeetype"])) $this->employeeType = $data["employeetype"];
        if(isset($data["givenname"])) $this->givenName = $data["givenname"];
        if(isset($data["homephone"])) $this->homePhone = $data["homephone"];
        if(isset($data["homepostaladdress"])) $this->homePostalAddress = $data["homepostaladdress"];
        if(isset($data["initials"])) $this->initials = $data["initials"];
        if(isset($data["jpegphoto"])) $this->jpegPhoto = $data["jpegphoto"];
        if(isset($data["labeleduri"])) $this->labeledURI = $data["labeleduri"];
        if(isset($data["mail"])) $this->mail = $data["mail"];
        if(isset($data["manager"])) $this->manager = $data["manager"];
        if(isset($data["mobile"])) $this->mobile = $data["mobile"];
        if(isset($data["o"])) $this->o = $data["o"];
        if(isset($data["pager"])) $this->pager = $data["pager"];
        if(isset($data["photo"])) $this->photo = $data["photo"];
        if(isset($data["roomnumber"])) $this->roomNumber = $data["roomnumber"];
        if(isset($data["secretary"])) $this->secretary = $data["secretary"];
        if(isset($data["uid"])) $this->uid = $data["uid"];
        if(isset($data["usercertificate"])) $this->userCertificate = $data["usercertificate"];
        if(isset($data["x500uniqueidentifier"])) $this->x500uniqueIdentifier = $data["x500uniqueidentifier"];
        if(isset($data["preferredlanguage"])) $this->preferredLanguage = $data["preferredlanguage"];
        if(isset($data["usersmimecertificate"])) $this->userSMIMECertificate = $data["usersmimecertificate"];
        if(isset($data["userpkcs12"])) $this->userPKCS12 = $data["userpkcs12"];
    }

    function setJPEGPhotoFromBlob($blob)
    {
        $attribs['jpegPhoto'] = $blob;
        $this->jpegPhoto[0] = $blob;
        $this->server->replaceAttribute($this->dn, $attribs);
    }
}

// vim: set tabstop=4 shiftwidth=4 expandtab:
?>
