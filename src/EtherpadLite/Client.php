<?php

namespace EtherpadLite;

use EtherpadLite\Exception\UnsupportedMethodException;

/**
 * Class Client
 *
 * @package EtherpadLite
 *
 * @method Response createGroup() creates a new group
 * @method Response createGroupIfNotExistsFor($groupMapper) this functions helps you to map your application group ids to etherpad lite group ids
 * @method Response deleteGroup($groupID) deletes a group
 * @method Response listPads($groupID) returns all pads of this group
 * @method Response createGroupPad($groupID, $padName, $text = null) creates a new pad in this group
 * @method Response listAllGroups() lists all existing groups
 *
 * @method Response createAuthor($name = null) creates a new author
 * @method Response createAuthorIfNotExistsFor($authorMapper, $name = null) this functions helps you to map your application author ids to etherpad lite author ids
 * @method Response listPadsOfAuthor($authorID) returns an array of all pads this author contributed to
 * @method Response getAuthorName($authorID) Returns the Author Name of the author
 *
 * @method Response createSession($groupID, $authorID, $validUntil) creates a new session. validUntil is an unix timestamp in seconds
 * @method Response deleteSession($sessionID) deletes a session by id
 * @method Response getSessionInfo($sessionID) returns informations about a session
 * @method Response listSessionsOfGroup($groupID) returns all sessions of a group
 * @method Response listSessionsOfAuthor($authorID) returns all sessions of an author
 *
 * @method Response getText($padID, $rev = null) returns the text of a pad
 * @method Response setText($padID, $text) sets the text of a pad
 * @method Response appendText($padID, $text) appends text to a pad
 * @method Response getHTML($padID, $rev = null) returns the text of a pad formatted as HTML
 * @method Response setHTML($padID, $html) sets the html of a pad
 * @method Response getAttributePool($padID) returns the attribute pool of a pad
 * @method Response getRevisionChangeset($padID, $revision = null) get the changeset at a given revision, or last revision if 'rev' is not defined.
 * @method Response createDiffHTML($padID, $startRev, $endRev) returns an object of diffs from 2 points in a pad
 * @method Response restoreRevision($padID, $revision) restores revision from past as new changeset
 *
 * @method Response getChatHistory($padID, $start = null, $end = null) a part of the chat history, when start and end are given, the whole chat histroy, when no extra parameters are given
 * @method Response getChatHead($padID) returns the chatHead (last number of the last chat-message) of the pad
 * @method Response appendChatMessage($padID, $text, $authorID, $time = null) creates a chat message, saves it to the database and sends it to all connected clients of this pad
 *
 * @method Response createPad($padID, $text = null) creates a new (non-group) pad. Note that if you need to create a group Pad, you should call createGroupPad.
 * @method Response getRevisionsCount($padID) returns the number of revisions of this pad
 * @method Response getSavedRevisionsCount($padID) returns the number of saved revisions of this pad
 * @method Response listSavedRevisions($padID) returns a list of saved revisions of this pad
 * @method Response saveRevision($padID, $revision = null) save a revision of a pad
 * @method Response padUsersCount($padID) returns the number of user that are currently editing this pad
 * @method Response padUsers($padID) returns the list of users that are currently editing this pad
 * @method Response deletePad($padID) deletes a pad
 * @method Response movePad($sourceID, $destinationID, $force = false) moves a pad. If force is true and the destination pad exists, it will be overwritten.
 * @method Response copyPad($sourceID, $destinationID, $force = false) copies a pad with full history and chat. If force is true and the destination pad exists, it will be overwritten.
 * @method Response getReadOnlyID($padID) returns the read only link of a pad
 * @method Response getPadID($readOnlyID) returns the id of a pad which is assigned to the readOnlyID
 * @method Response setPublicStatus($padID, $publicStatus) sets a boolean for the public status of a pad
 * @method Response getPublicStatus($padID) return true of false
 * @method Response setPassword($padID, $password) returns ok or a error message
 * @method Response isPasswordProtected($padID) returns true or false
 * @method Response listAuthorsOfPad($padID) returns an array of authors who contributed to this pad
 * @method Response getLastEdited($padID) returns the timestamp of the last revision of the pad
 * @method Response sendClientsMessage($padID, $msg) sends a custom message of type $msg to the pad
 * @method Response checkToken() returns ok when the current api token is valid
 *
 * @method Response listAllPads() lists all pads on this epl instance
 *
 */
class Client
{
    const API_VERSION = '1.2.13';

    private $apikey = null;
    private $url = null;

    /**
     * @param $apikey
     * @param string $url
     */
    public function __construct($apikey, $url = 'http://localhost:9001')
    {
        $this->apikey = $apikey;
        $this->url = $url;
    }

    /**
     * @param $method
     * @param array $args
     * @return Response
     * @throws Exception\UnsupportedMethodException
     */
    public function __call($method, $args = array())
    {
        if (!in_array($method, array_keys(self::getMethods()))) {
            throw new UnsupportedMethodException();
        }

        $request = new Request($this->url, $this->apikey, $method, $args);
        return new Response($request->send());
    }

    /**
     * Generates a random padID
     *
     * @return string
     */
    public function generatePadID()
    {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $length = 16;
        $padID = "";

        for ($i = 0; $i < $length; $i++) {
            $padID .= $chars[rand()%strlen($chars)];
        }

        return $padID;
    }

    /**
     * Array that holds the available methods and their required parameter names
     *
     * @return array
     */
    public static function getMethods()
    {
        return array(
            'createGroup' => array(),
            'createGroupIfNotExistsFor' => array('groupMapper'),
            'deleteGroup' => array('groupID'),
            'listPads' => array('groupID'),
            'createGroupPad' => array('groupID', 'padName', 'text'),
            'listAllGroups' => array(),
            'createAuthor' => array('name'),
            'createAuthorIfNotExistsFor' => array('authorMapper', 'name'),
            'listPadsOfAuthor' => array('authorID'),
            'getAuthorName' => array('authorID'),
            'createSession' => array('groupID', 'authorID', 'validUntil'),
            'deleteSession' => array('sessionID'),
            'getSessionInfo' => array('sessionID'),
            'listSessionsOfGroup' => array('groupID'),
            'listSessionsOfAuthor' => array('authorID'),
            'getText' => array('padID', 'rev'),
            'setText' => array('padID', 'text'),
            'appendText' => array('padID', 'text'),
            'getHTML' => array('padID', 'rev'),
            'setHTML' => array('padID', 'html'),
            'getAttributePool' => array('padID'),
            'createDiffHTML' => array('padID', 'startRev', 'endRev'),
            'restoreRevision' => array('padID', 'rev'),
            'getRevisionChangeset' => array('padID'),
            'getChatHistory' => array('padID', 'start', 'end'),
            'getChatHead' => array('padID'),
            'appendChatMessage' => array('padID', 'text', 'authorID'),
            'createPad' => array('padID', 'text'),
            'getRevisionsCount' => array('padID'),
            'getSavedRevisionsCount' => array('padID'),
            'listSavedRevisions' => array('padID'),
            'saveRevision' => array('padID'),
            'padUsersCount' => array('padID'),
            'padUsers' => array('padID'),
            'deletePad' => array('padID'),
            'movePad' => array('sourceID', 'destinationID'),
            'copyPad' => array('sourceID', 'destinationID'),
            'getReadOnlyID' => array('padID'),
            'getPadID' => array('readOnlyID'),
            'setPublicStatus' => array('padID', 'publicStatus'),
            'getPublicStatus' => array('padID'),
            'setPassword' => array('padID', 'password'),
            'isPasswordProtected' => array('padID'),
            'listAuthorsOfPad' => array('padID'),
            'getLastEdited' => array('padID'),
            'sendClientsMessage' => array('padID', 'msg'),
            'checkToken' => array(),
            'listAllPads' => array(),
        );
    }
}
