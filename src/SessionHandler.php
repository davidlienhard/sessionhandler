<?php
/**
 * contains \DavidLienhard\SessionHandler\SessionHandler class
 *
 * @package         tourBase
 * @author          David Lienhard <david.lienhard@tourasia.ch>
 * @copyright       tourasia
 */

declare(strict_types=1);

namespace DavidLienhard\SessionHandler;

use \DavidLienhard\Database\DatabaseInterface;
use \DavidLienhard\Database\Parameter as DBParam;
use \DavidLienhard\Database\ResultInterface;

/**
 * sessionhandler using database
 *
 * @author          David Lienhard <david.lienhard@tourasia.ch>
 * @copyright       tourasia
 */
class SessionHandler implements \SessionHandlerInterface
{
    /**
     * sets the given dependencies
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           \DavidLienhard\Database\DatabaseInterface   $db     database connection
     * @return          void
     * @uses            self::$db
     */
    public function __construct(private DatabaseInterface $db)
    {
    }

    /**
     * placeholder
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string      $savePath       path to save sessions to
     * @param           string      $sessionID      unique session id
     * @return          bool
     */
    public function open($savePath, $sessionID)
    {
        return true;
    }

    /**
     * cleanup
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @return          bool
     * @uses            self::$db
     */
    public function close()
    {
        $this->db = null;
        return true;
    }

    /**
     * read session data
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string      $sessionID      unique session id
     * @return          string      the session data
     * @uses            self::$db
     */
    public function read($sessionID)
    {
        try {
            $result = $this->db->query(
                "SELECT
                    `sessionData`
                FROM
                    `sessions`
                WHERE
                    `sessionID` = ?",
                new DBParam("s", $sessionID)
            );

            $sessionData = ($result instanceof ResultInterface) && $result->num_rows() === 1
                ? (string) $result->result(0, "sessionData")
                : "";
        } catch (\Exception $e) {
            throw $e;
        }

        return $sessionData;
    }

    /**
     * write session data
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string      $sessionID      unique session id
     * @param           string      $sessionData    data to be written to the session
     * @return          bool
     * @uses            self::$db
     */
    public function write($sessionID, $sessionData)
    {
        try {
            $this->db->query(
                "REPLACE INTO
                    `sessions`
                SET
                    `sessionID` = ?,
                    `sessionLastSave` = UNIX_TIMESTAMP(),
                    `sessionData` = ?",
                new DBParam("s", $sessionID),
                new DBParam("s", $sessionData)
            );
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }

    /**
     * destroys a session
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           string      $sessionID      unique session id
     * @return          bool
     * @uses            self::$db
     */
    public function destroy($sessionID)
    {
        try {
            $this->db->query(
                "DELETE FROM
                    `sessions`
                WHERE
                    `sessionID` = ?",
                new DBParam("s", $sessionID)
            );
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }

    /**
     * garbage collection
     *
     * @author          David Lienhard <david.lienhard@tourasia.ch>
     * @copyright       tourasia
     * @param           int     $maxLifetime    max lifetime of a session in seconds
     * @return          bool
     * @uses            self::$db
     */
    public function gc($maxLifetime)
    {
        try {
            $checkStamp = time() - $maxLifetime;

            $this->db->query(
                "DELETE FROM
                    `sessions`
                WHERE
                    `sessionLastSave` < ? OR
                    `sessionLastSave` IS NULL",
                new DBParam("i", $checkStamp)
            );
        } catch (\Exception $e) {
            throw $e;
        }

        return true;
    }
}
