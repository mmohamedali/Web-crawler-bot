<?php
/* DATABASE CONFIGURATION */
define('DB_NAME', 'bots_analytic'); /** The name of the database */
define('DB_USER', 'root');  /** MySQL database username */
define('DB_PASSWORD', ''); /** MySQL database password */
define('DB_HOST', 'localhost');/** MySQL hostname */

/* ADMIN AREA */
define('ADMIN_username', 'admin'); 
define('ADMIN_password', 'admin');  

/* BOTS ARRAY --->  3 ENTRIES MINIMUM*/
$defaultBots = array();
$defaultBots[] = array('id' => 1, 'name' => 'GoogleBot', 'mask' => 'googlebot', 'rdns' => 'googlebot.com');
$defaultBots[] = array('id' => 2, 'name' => 'BingBot', 'mask' => 'bingbot', 'rdns' => null);
$defaultBots[] = array('id' => 3, 'name' => 'YandexBot', 'mask' => 'yandex', 'rdns' => null);
$defaultBots[] = array('id' => 4, 'name' => 'Archive.org', 'mask' => 'archive.org_bot', 'rdns' => null);
$defaultBots[] = array('id' => 4, 'name' => 'MJ12', 'mask' => 'mj12', 'rdns' => null);
/***************************************************************
 ********** Configuration variables (ADVANCED USERS ************
 ***************************************************************/

/** 
 ** Turn on debug mode 
 ** This will display all the PHP errors and warnings
 **/
define('WP_DEBUG', true);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/** 
 ** Whether to use mysql or mysqli
 ** If left commented the script will automatically decide
 ** based upon the version of PHP
 */
//define('WP_USE_EXT_MYSQL', false);

/*************************************************
 ========= Configuration variables Ends ==========
 *************************************************/

define( 'OBJECT', 'OBJECT');
define( 'object', 'OBJECT' ); // Back compat.
define( 'OBJECT_K', 'OBJECT_K' );
define( 'ARRAY_A', 'ARRAY_A' );
define( 'ARRAY_N', 'ARRAY_N' );

/**
 ** For debugging this library prints some error messages on screen which can
 **	be turn OFF by following variable
 ** will work only if WP_DEBUG is true
 **/
define('WP_DEBUG_DISPLAY', true); 

/**
 * Database Access Abstraction Object
 */
class wpdb {

	/**
	 * Whether to show SQL/DB errors
	 */
	var $show_errors = false;

	/**
	 * Whether to suppress errors during the DB bootstrapping.
	 */
	var $suppress_errors = false;

	/**
	 * The last error during query.
	 */
	var $last_error = '';

	/**
	 * Amount of queries made
	 */
	var $num_queries = 0;

	/**
	 * Count of rows returned by previous query
	 */
	var $num_rows = 0;

	/**
	 * Count of affected rows by previous query
	 */
	var $rows_affected = 0;

	/**
	 * The ID generated for an AUTO_INCREMENT column by the previous query (usually INSERT).
	 */
	var $insert_id = 0;

	/**
	 * Last query made
	 */
	var $last_query;

	/**
	 * Results of the last query made
	 */
	var $last_result;

	/**
	 * MySQL result, which is either a resource or boolean.
	 */
	protected $result;

	/**
	 * Saved info on the table column
	 */
	protected $col_info;

	/**
	 * Saved queries that were executed
	 */
	var $queries;

	/**
     * The number of times to retry reconnecting before dying.
     */
	protected $reconnect_retries = 5;
	
	/**
	 * Whether the database queries are ready to start executing.
	 */
	var $ready = false;

	/**
	 * Format specifiers for DB columns. Columns not listed here default to %s. Initialized during WP load.
	 *
	 * Keys are column names, values are format types: 'ID' => '%d'
	 *
	 */
	var $field_types = array();

	/**
	 * Database table columns charset
	 */
	var $charset;

	/**
	 * Database table columns collate
	 */
	var $collate;

	/**
	 * Database Username
	 */
	protected $dbuser;

	/**
	 * Database Password
	 */
	protected $dbpassword;

	/**
	 * Database Name
	 */
	protected $dbname;

	/**
	 * Database Host
	 */
	protected $dbhost;

	/**
	 * Database Handle
	 */
	protected $dbh;

	/**
	 * A textual description of the last query/get_row/get_var call
	 */
	var $func_call;

	/**
     * Whether MySQL is used as the database engine.
     *
     * Set in WPDB::db_connect() to true, by default. 
     */
    public $is_mysql = null;

    /**
     * A list of incompatible SQL modes.
     */
    protected $incompatible_modes = array( 'NO_ZERO_DATE', 'ONLY_FULL_GROUP_BY',
        'STRICT_TRANS_TABLES', 'STRICT_ALL_TABLES', 'TRADITIONAL' );
 
    /**
     * Whether to use mysqli over mysql.
     */
    private $use_mysqli = false;
 
    /**
     * Whether we've managed to successfully connect at some point
     */
    private $has_connected = false;
 
	/**
	 * Connects to the database server and selects a database
	 *
	 * PHP5 style constructor for compatibility with PHP5. Does
	 * the actual setting up of the class properties and connection
	 * to the database.
	 *
	 * @param string $dbuser MySQL database user
	 * @param string $dbpassword MySQL database password
	 * @param string $dbname MySQL database name
	 * @param string $dbhost MySQL database host
	 */
	function __construct( $dbuser = DB_USER, $dbpassword = DB_PASSWORD, $dbname = DB_NAME, $dbhost = DB_HOST ) {
		
		if( empty($dbuser) || empty($dbname) || empty($dbhost) )
			$this->bail( "Please set the database details in wp-db.php" );

		register_shutdown_function( array( $this, '__destruct' ) );

		if ( WP_DEBUG && WP_DEBUG_DISPLAY )
			$this->show_errors();

		/* Use ext/mysqli if it exists and:
         *  - WP_USE_EXT_MYSQL is defined as false, or
         *  - We are a development version of WordPress, or
         *  - We are running PHP 5.5 or greater, or
         *  - ext/mysql is not loaded.
         */
        if ( function_exists( 'mysqli_connect' ) ) {
            if ( defined( 'WP_USE_EXT_MYSQL' ) ) {
                $this->use_mysqli = ! WP_USE_EXT_MYSQL;
            } elseif ( version_compare( phpversion(), '5.5', '>=' ) || ! function_exists( 'mysql_connect' ) ) {
                $this->use_mysqli = true;
            }
        }

		$this->init_charset();

		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;

		$this->db_connect();
	}

	/**
	 * PHP5 style destructor and will run when database object is destroyed.
	 * @return bool true
	 */
	function __destruct() {
		return true;
	}

	/**
	 * PHP5 style magic getter, used to lazy-load expensive data.
	 * @param string $name The private member to get, and optionally process
	 * @return mixed The private member
	 */
	function __get( $name ) {
		if ( 'col_info' == $name )
			$this->load_col_info();

		return $this->$name;
	}

	/**
	 * Magic function, for backwards compatibility
	 * @param string $name  The private member to set
	 * @param mixed  $value The value to set
	 */
	function __set( $name, $value ) {
		$this->$name = $value;
	}

	/**
	 * Magic function, for backwards compatibility
	 * @param string $name  The private member to check
	 *
	 * @return bool If the member is set or not
	 */
	function __isset( $name ) {
		return isset( $this->$name );
	}

	/**
	 * Magic function, for backwards compatibility
	 * @param string $name  The private member to unset
	 */
	function __unset( $name ) {
		unset( $this->$name );
	}

	/**
	 * Set $this->charset and $this->collate
	 */
	function init_charset() {
		if ( defined( 'DB_COLLATE' ) )
			$this->collate = DB_COLLATE;

		if ( defined( 'DB_CHARSET' ) )
			$this->charset = DB_CHARSET;
	}

	/**
	 * Sets the connection's character set.
	 * @param resource $dbh     The resource given by mysql_connect
	 * @param string   $charset The character set (optional)
	 * @param string   $collate The collation (optional)
	 */
	function set_charset( $dbh, $charset = null, $collate = null ) {
		if ( ! isset( $charset ) )
			$charset = $this->charset;
		if ( ! isset( $collate ) )
			$collate = $this->collate;
		if ( $this->has_cap( 'collation' ) && ! empty( $charset ) ) {
			if ( $this->use_mysqli ) {
                if ( function_exists( 'mysqli_set_charset' ) && $this->has_cap( 'set_charset' ) ) {
                    mysqli_set_charset( $dbh, $charset );
                } else {
                    $query = $this->prepare( 'SET NAMES %s', $charset );
                    if ( ! empty( $collate ) )
                        $query .= $this->prepare( ' COLLATE %s', $collate );
                    mysqli_query( $query, $dbh );
                }
            } else {
                if ( function_exists( 'mysql_set_charset' ) && $this->has_cap( 'set_charset' ) ) {
                    mysql_set_charset( $charset, $dbh );
                } else {
					$query = $this->prepare( 'SET NAMES %s', $charset );
					if ( ! empty( $collate ) )
					$query .= $this->prepare( ' COLLATE %s', $collate );
					mysql_query( $query, $dbh );	
				}
			}		
		}
	}

	/**
     * Change the current SQL mode, and ensure its WordPress compatibility.
     *
     * If no modes are passed, it will ensure the current MySQL server
     * modes are compatible.
     *
     * @since 3.9.0
     *
     * @param array $modes Optional. A list of SQL modes to set.
     */
    function set_sql_mode( $modes = array() ) {
        if ( empty( $modes ) ) {
            if ( $this->use_mysqli ) {
                $res = mysqli_query( $this->dbh, 'SELECT @@SESSION.sql_mode' );
            } else {
                $res = mysql_query( 'SELECT @@SESSION.sql_mode', $this->dbh );
            }
 
            if ( empty( $res ) ) {
                return;
            }
 
            if ( $this->use_mysqli ) {
                $modes_array = mysqli_fetch_array( $res );
                if ( empty( $modes_array[0] ) ) {
                    return;
                }
                $modes_str = $modes_array[0];
            } else {
                $modes_str = mysql_result( $res, 0 );
            }
 
            if ( empty( $modes_str ) ) {
                return;
            }
 
            $modes = explode( ',', $modes_str );
        }
 
        $modes = array_change_key_case( $modes, CASE_UPPER );
 
        /**
         * Filter the list of incompatible SQL modes to exclude.
         */
        $incompatible_modes = $this->incompatible_modes;
 
        foreach( $modes as $i => $mode ) {
            if ( in_array( $mode, $incompatible_modes ) ) {
                unset( $modes[ $i ] );
            }
        }
 
        $modes_str = implode( ',', $modes );
 
        if ( $this->use_mysqli ) {
            mysqli_query( $this->dbh, "SET SESSION sql_mode='$modes_str'" );
        } else {
            mysql_query( "SET SESSION sql_mode='$modes_str'", $this->dbh );
        }
    }

	/**
	 * Selects a database using the current database connection.
	 *
	 * The database name will be changed based on the current database
	 * connection. On failure, the execution will bail and display an DB error.
	 * @param string $db MySQL database name
	 * @param resource $dbh Optional link identifier.
	 * @return null Always null.
	 */
	function select( $db, $dbh = null ) {
		if ( is_null($dbh) )
			$dbh = $this->dbh;

		if ( $this->use_mysqli ) {
            $success = @mysqli_select_db( $dbh, $db );
        } else {
            $success = @mysql_select_db( $db, $dbh );
        }
        if ( ! $success ) {
        	$this->ready = false;
			$this->bail( "Unable to connect to database" );
			return;
        }
	}

	/**
	 * Real escape, using mysql_real_escape_string()
	 * @param  string $string to escape
	 * @return string escaped
	 */
	function _real_escape( $string ) {
		if ( $this->dbh ) {
            if ( $this->use_mysqli ) {
                return mysqli_real_escape_string( $this->dbh, $string );
            } else {
                return mysql_real_escape_string( $string, $this->dbh );
            }
        }
 
        $class = get_class( $this );
        $this->bail("$class must set a database connection for use with escaping.");
        return;
	}

	/**
	 * Escape data. Works on arrays.
	 * @param  string|array $data
	 * @return string|array escaped
	 */
	function _escape( $data ) {
		if ( is_array( $data ) ) {
			foreach ( $data as $k => $v ) {
				if ( is_array($v) )
					$data[$k] = $this->_escape( $v );
				else
					$data[$k] = $this->_real_escape( $v );
			}
		} else {
			$data = $this->_real_escape( $data );
		}

		return $data;
	}

	/**
	 * Escapes content by reference for insertion into the database, for security
	 * @param string $string to escape
	 * @return void
	 */
	function escape_by_ref( &$string ) {
		if ( ! is_float( $string ) )
			$string = $this->_real_escape( $string );
	}

	/**
	 * Prepares a SQL query for safe execution. Uses sprintf()-like syntax.
	 *
	 * The following directives can be used in the query format string:
	 *   %d (integer)
	 *   %f (float)
	 *   %s (string)
	 *   %% (literal percentage sign - no argument needed)
	 *
	 * All of %d, %f, and %s are to be left unquoted in the query string and they need an argument passed for them.
	 * Literals (%) as parts of the query must be properly written as %%.
	 *
	 * This function only supports a small subset of the sprintf syntax; it only supports %d (integer), %f (float), and %s (string).
	 * Does not support sign, padding, alignment, width or precision specifiers.
	 * Does not support argument numbering/swapping.
	 *
	 * May be called like {@link http://php.net/sprintf sprintf()} or like {@link http://php.net/vsprintf vsprintf()}.
	 *
	 * Both %d and %s should be left unquoted in the query string.
	 *
	 * <code>
	 * wpdb::prepare( "SELECT * FROM `table` WHERE `column` = %s AND `field` = %d", 'foo', 1337 )
	 * wpdb::prepare( "SELECT DATE_FORMAT(`field`, '%%c') FROM `table` WHERE `column` = %s", 'foo' );
	 * </code>
	 *
	 * @link http://php.net/sprintf Description of syntax.
	 *
	 * @param string $query Query statement with sprintf()-like placeholders
	 * @param array|mixed $args The array of variables to substitute into the query's placeholders if being called like
	 * 	{@link http://php.net/vsprintf vsprintf()}, or the first variable to substitute into the query's placeholders if
	 * 	being called like {@link http://php.net/sprintf sprintf()}.
	 * @param mixed $args,... further variables to substitute into the query's placeholders if being called like
	 * 	{@link http://php.net/sprintf sprintf()}.
	 * @return null|false|string Sanitized query string, null if there is no query, false if there is an error and string
	 * 	if there was something to prepare
	 */
	function prepare( $query, $args ) {
		if ( is_null( $query ) )
			return;

		// This is not meant to be foolproof -- but it will catch obviously incorrect usage.
        if ( strpos( $query, '%' ) === false ) {
            $this->bail( 'The query argument of prepare must have a placeholder.' );
        }

		$args = func_get_args();
		array_shift( $args );
		// If args were passed as an array (as in vsprintf), move them up
		if ( isset( $args[0] ) && is_array($args[0]) )
			$args = $args[0];
		$query = str_replace( "'%s'", '%s', $query ); // in case someone mistakenly already singlequoted it
		$query = str_replace( '"%s"', '%s', $query ); // doublequote unquoting
		$query = preg_replace( '|(?<!%)%f|' , '%F', $query ); // Force floats to be locale unaware
		$query = preg_replace( '|(?<!%)%s|', "'%s'", $query ); // quote the strings, avoiding escaped strings like %%s
		array_walk( $args, array( $this, 'escape_by_ref' ) );
		return @vsprintf( $query, $args );
	}

	/**
	 * Print SQL/DB error.
	 *
	 * @since 0.71
	 * @global array $EZSQL_ERROR Stores error information of query and error string
	 *
	 * @param string $str The error to display
	 * @return bool False if the showing of errors is disabled.
	 */
	function print_error( $str = '' ) {
		global $EZSQL_ERROR;

		if ( !$str ) {
            if ( $this->use_mysqli ) {
                $str = mysqli_error( $this->dbh );
            } else {
                $str = mysql_error( $this->dbh );
            }
        }
		
		$EZSQL_ERROR[] = array( 'query' => $this->last_query, 'error_str' => $str );

		if ( $this->suppress_errors )
			return false;
		
		if ( $caller = $this->get_caller() )
			$error_str = sprintf( 'WordPress database error %1$s for query %2$s made by %3$s', $str, $this->last_query, $caller );
		else
			$error_str = sprintf( 'WordPress database error %1$s for query %2$s', $str, $this->last_query );

		error_log( $error_str );

		// Are we showing errors?
		if ( ! $this->show_errors )
			return false;

		
        $str   = htmlspecialchars( $str, ENT_QUOTES );
        $query = htmlspecialchars( $this->last_query, ENT_QUOTES );

        print "<div id='error'>
        <p class='wpdberror'><strong>WordPress database error:</strong> [$str]<br />
        <code>$query</code></p>
        </div>";
		
	}

	/**
	 * Enables showing of database errors.
	 *
	 * This function should be used only to enable showing of errors.
	 * wpdb::hide_errors() should be used instead for hiding of errors. However,
	 * this function can be used to enable and disable showing of database
	 * errors.
	 *
	 * @param bool $show Whether to show or hide errors
	 * @return bool Old value for showing errors.
	 */
	function show_errors( $show = true ) {
		$errors = $this->show_errors;
		$this->show_errors = $show;
		return $errors;
	}

	/**
	 * Disables showing of database errors.
	 *
	 * By default database errors are not shown.
	 * @return bool Whether showing of errors was active
	 */
	function hide_errors() {
		$show = $this->show_errors;
		$this->show_errors = false;
		return $show;
	}

	/**
	 * Whether to suppress database errors.
	 *
	 * By default database errors are suppressed, with a simple
	 * call to this function they can be enabled.
	 * @param bool $suppress Optional. New value. Defaults to true.
	 * @return bool Old value
	 */
	function suppress_errors( $suppress = true ) {
		$errors = $this->suppress_errors;
		$this->suppress_errors = (bool) $suppress;
		return $errors;
	}

	/**
	 * Kill cached query results.
	 * @return void
	 */
	function flush() {
		$this->last_result = array();
		$this->col_info    = null;
		$this->last_query  = null;
		$this->rows_affected = $this->num_rows = 0;
		$this->last_error  = '';

		if ( is_resource( $this->result ) ) {
            if ( $this->use_mysqli ) {
                mysqli_free_result( $this->result );
            } else {
                mysql_free_result( $this->result );
            }
        }
	}

	/**
	 * Connect to and select database
	 */
	function db_connect( $allow_bail = true ) {
 
        $this->is_mysql = true;
 
        $new_link = defined( 'MYSQL_NEW_LINK' ) ? MYSQL_NEW_LINK : true;
        $client_flags = defined( 'MYSQL_CLIENT_FLAGS' ) ? MYSQL_CLIENT_FLAGS : 0;
 
        if ( $this->use_mysqli ) {
            $this->dbh = mysqli_init();
 
            // mysqli_real_connect doesn't support the host param including a port or socket
            // like mysql_connect does. This duplicates how mysql_connect detects a port and/or socket file.
            $port = null;
            $socket = null;
            $host = $this->dbhost;
            $port_or_socket = strstr( $host, ':' );
            if ( ! empty( $port_or_socket ) ) {
                $host = substr( $host, 0, strpos( $host, ':' ) );
                $port_or_socket = substr( $port_or_socket, 1 );
                if ( 0 !== strpos( $port_or_socket, '/' ) ) {
                    $port = intval( $port_or_socket );
                    $maybe_socket = strstr( $port_or_socket, ':' );
                    if ( ! empty( $maybe_socket ) ) {
                        $socket = substr( $maybe_socket, 1 );
                    }
                } else {
                    $socket = $port_or_socket;
                }
            }
 
            if ( WP_DEBUG ) {
                mysqli_real_connect( $this->dbh, $host, $this->dbuser, $this->dbpassword, null, $port, $socket, $client_flags );
            } else {
                @mysqli_real_connect( $this->dbh, $host, $this->dbuser, $this->dbpassword, null, $port, $socket, $client_flags );
            }
 
            if ( $this->dbh->connect_errno ) {
                $this->dbh = null;
 
                /* It's possible ext/mysqli is misconfigured. Fall back to ext/mysql if:
                 *  - We haven't previously connected, and
                 *  - WP_USE_EXT_MYSQL isn't set to false, and
                 *  - ext/mysql is loaded.
                 */
                $attempt_fallback = true;
 
                if ( $this->has_connected ) {
                    $attempt_fallback = false;
                } else if ( defined( 'WP_USE_EXT_MYSQL' ) && ! WP_USE_EXT_MYSQL ) {
                    $attempt_fallback = false;
                } else if ( ! function_exists( 'mysql_connect' ) ) {
                    $attempt_fallback = false;
                }
 
                if ( $attempt_fallback ) {
                    $this->use_mysqli = false;
                    $this->db_connect();
                }
            }
        } else {
            if ( WP_DEBUG ) {
                $this->dbh = mysql_connect( $this->dbhost, $this->dbuser, $this->dbpassword, $new_link, $client_flags );
            } else {
                $this->dbh = @mysql_connect( $this->dbhost, $this->dbuser, $this->dbpassword, $new_link, $client_flags );
            }
        }
 
        if ( ! $this->dbh && $allow_bail ) {
            $this->bail( "
<h1>Error establishing a database connection</h1>
<p>This either means that the username and password information is incorrect or we can't contact the database server. This could mean your host's database server is down.</p>
<ul>
    <li>Are you sure you have the correct username and password?</li>
    <li>Are you sure that you have typed the correct hostname?</li>
    <li>Are you sure that the database server is running?</li>
</ul>" );
 
            return false;
        } else if ( $this->dbh ) {
            $this->has_connected = true;
 
            $this->set_charset( $this->dbh );
            $this->set_sql_mode();
 
            $this->ready = true;
 
            $this->select( $this->dbname, $this->dbh );
 
            return true;
        }
 
        return false;
    }

    /**
     * Check that the connection to the database is still up. If not, try to reconnect.
     *
     * If this function is unable to reconnect, it will forcibly die
     *
     * If $allow_bail is false, the lack of database connection will need
     * to be handled manually.
     */
    function check_connection( $allow_bail = true ) {
        if ( $this->use_mysqli ) {
            if ( @mysqli_ping( $this->dbh ) ) {
                return true;
            }
        } else {
            if ( @mysql_ping( $this->dbh ) ) {
                return true;
            }
        }
 
        $error_reporting = false;
 
        // Disable warnings, as we don't want to see a multitude of "unable to connect" messages
        if ( WP_DEBUG ) {
            $error_reporting = error_reporting();
            error_reporting( $error_reporting & ~E_WARNING );
        }
 
        for ( $tries = 1; $tries <= $this->reconnect_retries; $tries++ ) {
            // On the last try, re-enable warnings. We want to see a single instance of the
            // "unable to connect" message on the bail() screen, if it appears.
            if ( $this->reconnect_retries === $tries && WP_DEBUG ) {
                error_reporting( $error_reporting );
            }
 
            if ( $this->db_connect( false ) ) {
                if ( $error_reporting ) {
                    error_reporting( $error_reporting );
                }
 
                return true;
            }
 
            sleep( 1 );
        }

        if ( ! $allow_bail ) {
            return false;
        }
 
        // We weren't able to reconnect, so we better bail.
        $this->bail( "
<h1>Error reconnecting to the database</h1>
<p>This means that we lost contact with the database server. This could mean your host's database server is down.</p>
<ul>
    <li>Are you sure that the database server is running?</li>
    <li>Are you sure that the database server is not under particularly heavy load?</li>
</ul>" );
 
        // Call die() if bail didn't die, because this database is no more. It has ceased to be (at least temporarily).
        die();
    }

	/**
	 * Perform a MySQL database query, using current database connection.
	 * @param string $query Database query
	 * @return int|false Number of rows affected/selected or false on error
	 */
	function query( $query ) {
		if ( ! $this->ready )
			return false;
		
		$return_val = 0;
		$this->flush();

		// Log how the function was called
		$this->func_call = "\$db->query(\"$query\")";

		// Keep track of the last query for debug..
		$this->last_query = $query;

		$this->_do_query( $query );
 
        // MySQL server has gone away, try to reconnect
        $mysql_errno = 0;
        if ( ! empty( $this->dbh ) ) {
            if ( $this->use_mysqli ) {
                $mysql_errno = mysqli_errno( $this->dbh );
            } else {
                $mysql_errno = mysql_errno( $this->dbh );
            }
        }
 
        if ( empty( $this->dbh ) || 2006 == $mysql_errno ) {
            if ( $this->check_connection() ) {
                $this->_do_query( $query );
            } else {
                $this->insert_id = 0;
                return false;
            }
        }
 
        // If there is an error then take note of it..
        if ( $this->use_mysqli ) {
            $this->last_error = mysqli_error( $this->dbh );
        } else {
            $this->last_error = mysql_error( $this->dbh );
        }
 
        if ( $this->last_error ) {
            // Clear insert_id on a subsequent failed insert.
            if ( $this->insert_id && preg_match( '/^\s*(insert|replace)\s/i', $query ) )
                $this->insert_id = 0;
 
            $this->print_error();
            return false;
        }
 
        if ( preg_match( '/^\s*(create|alter|truncate|drop)\s/i', $query ) ) {
            $return_val = $this->result;
        } elseif ( preg_match( '/^\s*(insert|delete|update|replace)\s/i', $query ) ) {
            if ( $this->use_mysqli ) {
                $this->rows_affected = mysqli_affected_rows( $this->dbh );
            } else {
                $this->rows_affected = mysql_affected_rows( $this->dbh );
            }
            // Take note of the insert_id
            if ( preg_match( '/^\s*(insert|replace)\s/i', $query ) ) {
                if ( $this->use_mysqli ) {
                    $this->insert_id = mysqli_insert_id( $this->dbh );
                } else {
                    $this->insert_id = mysql_insert_id( $this->dbh );
                }
            }
            // Return number of rows affected
            $return_val = $this->rows_affected;
        } else {
            $num_rows = 0;
            if ( $this->use_mysqli ) {
                while ( $row = @mysqli_fetch_object( $this->result ) ) {
                    $this->last_result[$num_rows] = $row;
                    $num_rows++;
                }
            } else {
                while ( $row = @mysql_fetch_object( $this->result ) ) {
                    $this->last_result[$num_rows] = $row;
                    $num_rows++;
                }
            }
 
            // Log number of rows the query returned
            // and return number of rows selected
            $this->num_rows = $num_rows;
            $return_val     = $num_rows;
        }

		return $return_val;
	}

	/**
     * Internal function to perform the mysql_query() call.
     * @param string $query The query to run.
     */
    private function _do_query( $query ) {
        if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
            $this->timer_start();
        }
 
        if ( $this->use_mysqli ) {
            $this->result = @mysqli_query( $this->dbh, $query );
        } else {
            $this->result = @mysql_query( $query, $this->dbh );
        }
        $this->num_queries++;
 
        if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
            $this->queries[] = array( $query, $this->timer_stop(), $this->get_caller() );
        }
    }

	/**
	 * Insert a row into a table.
	 *
	 * <code>
	 * wpdb::insert( 'table', array( 'column' => 'foo', 'field' => 'bar' ) )
	 * wpdb::insert( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( '%s', '%d' ) )
	 * </code>
	 *
	 * @param string $table table name
	 * @param array $data Data to insert (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string, that format will be used for all of the values in $data.
	 * 	A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
	 * @return int|false The number of rows inserted, or false on error.
	 */
	function insert( $table, $data, $format = null ) {
		return $this->_insert_replace_helper( $table, $data, $format, 'INSERT' );
	}

	/**
	 * Replace a row into a table.
	 *
	 * <code>
	 * wpdb::replace( 'table', array( 'column' => 'foo', 'field' => 'bar' ) )
	 * wpdb::replace( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( '%s', '%d' ) )
	 * </code>
	 *
	 * @param string $table table name
	 * @param array $data Data to insert (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string, that format will be used for all of the values in $data.
	 * 	A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
	 * @return int|false The number of rows affected, or false on error.
	 */
	function replace( $table, $data, $format = null ) {
		return $this->_insert_replace_helper( $table, $data, $format, 'REPLACE' );
	}

	/**
	 * Helper function for insert and replace.
	 *
	 * Runs an insert or replace query based on $type argument.
	 *
	 * @param string $table table name
	 * @param array $data Data to insert (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param array|string $format Optional. An array of formats to be mapped to each of the value in $data. If string, that format will be used for all of the values in $data.
	 * 	A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
	 * @param string $type Optional. What type of operation is this? INSERT or REPLACE. Defaults to INSERT.
	 * @return int|false The number of rows affected, or false on error.
	 */
	function _insert_replace_helper( $table, $data, $format = null, $type = 'INSERT' ) {
		if ( ! in_array( strtoupper( $type ), array( 'REPLACE', 'INSERT' ) ) )
			return false;
		$this->insert_id = 0;
		$formats = $format = (array) $format;
		$fields = array_keys( $data );
		$formatted_fields = array();
		foreach ( $fields as $field ) {
			if ( !empty( $format ) )
				$form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
			elseif ( isset( $this->field_types[$field] ) )
				$form = $this->field_types[$field];
			else
				$form = '%s';
			$formatted_fields[] = $form;
		}
		$sql = "{$type} INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES (" . implode( ",", $formatted_fields ) . ")";
		return $this->query( $this->prepare( $sql, $data ) );
	}

	/**
	 * Update a row in the table
	 *
	 * <code>
	 * wpdb::update( 'table', array( 'column' => 'foo', 'field' => 'bar' ), array( 'ID' => 1 ) )
	 * wpdb::update( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( 'ID' => 1 ), array( '%s', '%d' ), array( '%d' ) )
	 * </code>
	 * 
	 * @param string $table table name
	 * @param array $data Data to update (in column => value pairs). Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param array $where A named array of WHERE clauses (in column => value pairs). Multiple clauses will be joined with ANDs. Both $where columns and $where values should be "raw".
	 * @param array|string $format Optional. An array of formats to be mapped to each of the values in $data. If string, that format will be used for all of the values in $data.
	 * 	A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $data will be treated as strings unless otherwise specified in wpdb::$field_types.
	 * @param array|string $where_format Optional. An array of formats to be mapped to each of the values in $where. If string, that format will be used for all of the items in $where. A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $where will be treated as strings.
	 * @return int|false The number of rows updated, or false on error.
	 */
	function update( $table, $data, $where, $format = null, $where_format = null ) {
		if ( ! is_array( $data ) || ! is_array( $where ) )
			return false;

		$formats = $format = (array) $format;
		$bits = $wheres = array();
		foreach ( (array) array_keys( $data ) as $field ) {
			if ( !empty( $format ) )
				$form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
			elseif ( isset($this->field_types[$field]) )
				$form = $this->field_types[$field];
			else
				$form = '%s';
			$bits[] = "`$field` = {$form}";
		}

		$where_formats = $where_format = (array) $where_format;
		foreach ( (array) array_keys( $where ) as $field ) {
			if ( !empty( $where_format ) )
				$form = ( $form = array_shift( $where_formats ) ) ? $form : $where_format[0];
			elseif ( isset( $this->field_types[$field] ) )
				$form = $this->field_types[$field];
			else
				$form = '%s';
			$wheres[] = "`$field` = {$form}";
		}

		$sql = "UPDATE `$table` SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres );
		return $this->query( $this->prepare( $sql, array_merge( array_values( $data ), array_values( $where ) ) ) );
	}

	/**
	 * Delete a row in the table
	 *
	 * <code>
	 * wpdb::delete( 'table', array( 'ID' => 1 ) )
	 * wpdb::delete( 'table', array( 'ID' => 1 ), array( '%d' ) )
	 * </code>
	 *
	 * @param string $table table name
	 * @param array $where A named array of WHERE clauses (in column => value pairs). Multiple clauses will be joined with ANDs. Both $where columns and $where values should be "raw".
	 * @param array|string $where_format Optional. An array of formats to be mapped to each of the values in $where. If string, that format will be used for all of the items in $where. A format is one of '%d', '%f', '%s' (integer, float, string). If omitted, all values in $where will be treated as strings unless otherwise specified in wpdb::$field_types.
	 * @return int|false The number of rows updated, or false on error.
	 */
	function delete( $table, $where, $where_format = null ) {
		if ( ! is_array( $where ) )
			return false;

		$bits = $wheres = array();

		$where_formats = $where_format = (array) $where_format;

		foreach ( array_keys( $where ) as $field ) {
			if ( !empty( $where_format ) ) {
				$form = ( $form = array_shift( $where_formats ) ) ? $form : $where_format[0];
			} elseif ( isset( $this->field_types[ $field ] ) ) {
				$form = $this->field_types[ $field ];
			} else {
				$form = '%s';
			}

			$wheres[] = "$field = $form";
		}

		$sql = "DELETE FROM $table WHERE " . implode( ' AND ', $wheres );
		return $this->query( $this->prepare( $sql, $where ) );
	}


	/**
	 * Retrieve one variable from the database.
	 *
	 * Executes a SQL query and returns the value from the SQL result.
	 * If the SQL result contains more than one column and/or more than one row, this function returns the value in the column and row specified.
	 * If $query is null, this function returns the value in the specified column and row from the previous SQL result.
	 *
	 * @param string|null $query Optional. SQL query. Defaults to null, use the result from the previous query.
	 * @param int $x Optional. Column of value to return. Indexed from 0.
	 * @param int $y Optional. Row of value to return. Indexed from 0.
	 * @return string|null Database query result (as string), or null on failure
	 */
	function get_var( $query = null, $x = 0, $y = 0 ) {
		$this->func_call = "\$db->get_var(\"$query\", $x, $y)";
		if ( $query )
			$this->query( $query );

		// Extract var out of cached results based x,y vals
		if ( !empty( $this->last_result[$y] ) ) {
			$values = array_values( get_object_vars( $this->last_result[$y] ) );
		}

		// If there is a value return it else return null
		return ( isset( $values[$x] ) && $values[$x] !== '' ) ? $values[$x] : null;
	}

	/**
	 * Retrieve one row from the database.
	 *
	 * Executes a SQL query and returns the row from the SQL result.
	 *
	 * @param string|null $query SQL query.
	 * @param string $output Optional. one of ARRAY_A | ARRAY_N | OBJECT constants. Return an associative array (column => value, ...),
	 * 	a numerically indexed array (0 => value, ...) or an object ( ->column = value ), respectively.
	 * @param int $y Optional. Row to return. Indexed from 0.
	 * @return mixed Database query result in format specified by $output or null on failure
	 */
	function get_row( $query = null, $output = OBJECT, $y = 0 ) {
		$this->func_call = "\$db->get_row(\"$query\",$output,$y)";
		if ( $query )
			$this->query( $query );
		else
			return null;

		if ( !isset( $this->last_result[$y] ) )
			return null;

		if ( $output == OBJECT ) {
			return $this->last_result[$y] ? $this->last_result[$y] : null;
		} elseif ( $output == ARRAY_A ) {
			return $this->last_result[$y] ? get_object_vars( $this->last_result[$y] ) : null;
		} elseif ( $output == ARRAY_N ) {
			return $this->last_result[$y] ? array_values( get_object_vars( $this->last_result[$y] ) ) : null;
		} elseif ( strtoupper( $output ) === OBJECT ) {
            // Back compat for OBJECT being previously case insensitive.
            return $this->last_result[$y] ? $this->last_result[$y] : null;
        } else {
			$this->print_error( " \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N" );
		}
	}

	/**
	 * Retrieve one column from the database.
	 *
	 * Executes a SQL query and returns the column from the SQL result.
	 * If the SQL result contains more than one column, this function returns the column specified.
	 * If $query is null, this function returns the specified column from the previous SQL result.
	 *
	 * @param string|null $query Optional. SQL query. Defaults to previous query.
	 * @param int $x Optional. Column to return. Indexed from 0.
	 * @return array Database query result. Array indexed from 0 by SQL result row number.
	 */
	function get_col( $query = null , $x = 0 ) {
		if ( $query )
			$this->query( $query );

		$new_array = array();
		// Extract the column values
		for ( $i = 0, $j = count( $this->last_result ); $i < $j; $i++ ) {
			$new_array[$i] = $this->get_var( null, $x, $i );
		}
		return $new_array;
	}

	/**
	 * Retrieve an entire SQL result set from the database (i.e., many rows)
	 *
	 * Executes a SQL query and returns the entire SQL result.
	 *
	 * @param string $query SQL query.
	 * @param string $output Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants. With one of the first three, return an array of rows indexed from 0 by SQL result row number.
	 * 	Each row is an associative array (column => value, ...), a numerically indexed array (0 => value, ...), or an object. ( ->column = value ), respectively.
	 * 	With OBJECT_K, return an associative array of row objects keyed by the value of each row's first column's value. Duplicate keys are discarded.
	 * @return mixed Database query results
	 */
	function get_results( $query = null, $output = OBJECT ) {
		$this->func_call = "\$db->get_results(\"$query\", $output)";

		if ( $query )
			$this->query( $query );
		else
			return null;

		$new_array = array();
		if ( $output == OBJECT ) {
			// Return an integer-keyed array of row objects
			return $this->last_result;
		} elseif ( $output == OBJECT_K ) {
			// Return an array of row objects with keys from column 1
			// (Duplicates are discarded)
			foreach ( $this->last_result as $row ) {
				$var_by_ref = get_object_vars( $row );
				$key = array_shift( $var_by_ref );
				if ( ! isset( $new_array[ $key ] ) )
					$new_array[ $key ] = $row;
			}
			return $new_array;
		} elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
			// Return an integer-keyed array of...
			if ( $this->last_result ) {
				foreach( (array) $this->last_result as $row ) {
					if ( $output == ARRAY_N ) {
						// ...integer-keyed row arrays
						$new_array[] = array_values( get_object_vars( $row ) );
					} else {
						// ...column name-keyed row arrays
						$new_array[] = get_object_vars( $row );
					}
				}
			}
			return $new_array;
		} elseif ( strtoupper( $output ) === OBJECT ) {
            // Back compat for OBJECT being previously case insensitive.
            return $this->last_result;
        }
		return null;
	}

	/**
	 * Load the column metadata from the last query.
	 *
	 * @access protected
	 */
	protected function load_col_info() {
		if ( $this->col_info )
			return;

		if ( $this->use_mysqli ) {
            for ( $i = 0; $i < @mysqli_num_fields( $this->result ); $i++ ) {
                $this->col_info[ $i ] = @mysqli_fetch_field( $this->result );
            }
        } else {
			for ( $i = 0; $i < @mysql_num_fields( $this->result ); $i++ ) {
				$this->col_info[ $i ] = @mysql_fetch_field( $this->result, $i );
			}
		}
	}

	/**
	 * Retrieve column metadata from the last query.
	 *
	 * @param string $info_type Optional. Type one of name, table, def, max_length, not_null, primary_key, multiple_key, unique_key, numeric, blob, type, unsigned, zerofill
	 * @param int $col_offset Optional. 0: col name. 1: which table the col's in. 2: col's max length. 3: if the col is numeric. 4: col's type
	 * @return mixed Column Results
	 */
	function get_col_info( $info_type = 'name', $col_offset = -1 ) {
		$this->load_col_info();

		if ( $this->col_info ) {
			if ( $col_offset == -1 ) {
				$i = 0;
				$new_array = array();
				foreach( (array) $this->col_info as $col ) {
					$new_array[$i] = $col->{$info_type};
					$i++;
				}
				return $new_array;
			} else {
				return $this->col_info[$col_offset]->{$info_type};
			}
		}
	}

	/**
	 * Starts the timer, for debugging purposes.
	 *
	 * @return true
	 */
	function timer_start() {
		$this->time_start = microtime( true );
		return true;
	}

	/**
	 * Stops the debugging timer.
	 *
	 * @return float Total time spent on the query, in seconds
	 */
	function timer_stop() {
		return ( microtime( true ) - $this->time_start );
	}

	/**
	 * Wraps errors in a nice header and footer and dies.
	 *
	 * Will not die if wpdb::$show_errors is false.
	 *
	 * @param string $message The Error message
	 * @return false|void
	 */
	function bail( $message) {
		if ( !$this->show_errors ) {
			$this->error = $message;
			return false;
		}
		die($message);
	}

	/**
	 * Whether MySQL database is at least the required minimum version.
	 *
	 * @return WP_Error
	 */
	function check_database_version() {
		global $wp_version, $required_mysql_version;
		// Make sure the server has the required MySQL version
		if ( version_compare($this->db_version(), $required_mysql_version, '<') )
			return new WP_Error('database_version', sprintf( __( '<strong>ERROR</strong>: WordPress %1$s requires MySQL %2$s or higher' ), $wp_version, $required_mysql_version ));
	}

	/**
	 * Whether the database supports collation.
	 *
	 * @since 2.5.0
	 * @deprecated 3.5.0
	 * @deprecated Use wpdb::has_cap( 'collation' )
	 *
	 * @return bool True if collation is supported, false if version does not
	 */
	function supports_collation() {
		_deprecated_function( __FUNCTION__, '3.5', 'wpdb::has_cap( \'collation\' )' );
		return $this->has_cap( 'collation' );
	}

	/**
	 * The database character collate.
	 *
	 * @return string The database character collate.
	 */
	public function get_charset_collate() {
		$charset_collate = '';

		if ( ! empty( $this->charset ) )
			$charset_collate = "DEFAULT CHARACTER SET $this->charset";
		if ( ! empty( $this->collate ) )
			$charset_collate .= " COLLATE $this->collate";

		return $charset_collate;
	}

	/**
	 * Determine if a database supports a particular feature.
	 *
	 * @param string $db_cap The feature to check for.
	 * @return bool
	 */
	function has_cap( $db_cap ) {
		$version = $this->db_version();

		switch ( strtolower( $db_cap ) ) {
			case 'collation' :    // @since 2.5.0
			case 'group_concat' : // @since 2.7.0
			case 'subqueries' :   // @since 2.7.0
				return version_compare( $version, '4.1', '>=' );
			case 'set_charset' :
				return version_compare( $version, '5.0.7', '>=' );
		};

		return false;
	}

	/**
	 * Retrieve the name of the function that called wpdb.
	 *
	 * Searches up the list of functions until it reaches
	 * the one that would most logically had called this method.
	 *
	 * @return string The name of the calling function
	 */
	function get_caller() {
		return $this->debug_backtrace_summary( __CLASS__ );
	}

	function debug_backtrace_summary( $ignore_class = null, $skip_frames = 0, $pretty = true ) {
		if ( version_compare( PHP_VERSION, '5.2.5', '>=' ) )
			$trace = debug_backtrace( false );
		else
			$trace = debug_backtrace();

		$caller = array();
		$check_class = ! is_null( $ignore_class );
		$skip_frames++; // skip this function

		foreach ( $trace as $call ) {
			if ( $skip_frames > 0 ) {
				$skip_frames--;
			} 
			elseif ( isset( $call['class'] ) ) {
				if ( $check_class && $ignore_class == $call['class'] )
					continue; // Filter out calls

				$caller[] = "{$call['class']}{$call['type']}{$call['function']}";
			} 
			else {
					$caller[] = $call['function'];
			}
			
		}
		if ( $pretty )
			return join( ', ', array_reverse( $caller ) );
		else
			return $caller;
	}

	/**
	 * The database version number.
	 *
	 * @return false|string false on failure, version number on success
	 */
	function db_version() {
		if ( $this->use_mysqli ) {
            $server_info = mysqli_get_server_info( $this->dbh );
        } else {
            $server_info = mysql_get_server_info( $this->dbh );
        }
        return preg_replace( '/[^0-9.].*/', '', $server_info );
	}
	
}

//Create an object of the wpdb class
$wpdb = new wpdb();