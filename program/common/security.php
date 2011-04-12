<?php

namespace Common;

class Security
{
    // Filter method. This modifies the input and returns it.
    
    public static function filter($input, $filter)
    {
        switch ($filter)
        {
            // Integer.
            
            case 'int':
            case 'integer':
                if ($input > PHP_INT_MAX) return round((float)$input);
                return (int)$input;
            
            // Float.
            
            case 'float':
                return (float)$input;
            
            // Plain text, escaped.
            
            case 'escape':
                if (!mb_check_encoding($input, 'UTF-8')) return false;
                return trim(htmlentities($input, ENT_QUOTES, 'UTF-8', false));
            
            // Plain text, escaped and stripped of tags.
            
            case 'strip':
                if (!mb_check_encoding($input, 'UTF-8')) return false;
                return trim(htmlentities(strip_tags($input), ENT_QUOTES, 'UTF-8', false));
            
            // Filename.
            
            case 'filename':
                
                // If not valid UTF-8, return false.
                
                if (!mb_check_encoding($input, 'UTF-8')) return false;
                
                // Replace potentially unsafe characters with safe characters, while trying to retain meaning.
                
                $illegal = array('\\', '/', '<', '>', '{', '}', ':', ';', '|', '"', '~', '`', '@', '#', '$', '%', '^', '&', '*', '?');
                $replace = array('', '', '(', ')', '(', ')', '_', ',', '_', '', '_', '\'', '_', '_', '_', '_', '_', '_', '', '');
                $output = str_replace($illegal, $replace, $input);
                
                // Remove ASCII control characters (00-1F and 7F, FF).
                
                $output = preg_replace('/([\\x00-\\x1f\\x7f\\xff]+)/', '', $output);
                
                // Remove extra dots and/or replacement symbols.
                
                $output = trim($output, ' .-_');
                $output = preg_replace('/__+/', '_', $output);
                
                // If the filename is over 100 characters long, truncate it.
                
                if (mb_strlen($output, 'UTF-8') > 100)
                {
                    $pos = strrpos($output, '.');
                    if ($pos === false)
                    {
                        $output = mb_substr($output, 0, 100, 'UTF-8');
                    }
                    else
                    {
                        $ext = substr($output, $pos);
                        if (mb_strlen($ext, 'UTF-8') > 10) $ext = mb_substr($ext, 0, 10);
                        $output = mb_substr($output, 0, 100 - mb_strlen($ext)) . $ext;
                    }
                }
                
                // If the extension is ".php", convert to ".phps" so that it will not be executed by the web server.
                
                if (strtolower(substr($output, strlen($output) - 4)) === '.php')
                {
                    $output = substr($output, 0, strlen($output) - 4) . '.phps';
                }
                
                return $output;
            
            // No change.
            
            default: return $input;
        }
    }
    
    // Validate method. This only ever returns true or false.
    
    public static function validate($input, $rules)
    {
        // Parse the rules.
        
        $rules = explode(',', $rules);
        $first_rule = array_shift($rules);
        $rule_count = count($rules);
        for ($i = 0; $i < $rule_count; $i++)
        {
            $r = explode('=', $rules[$i]);
            $rules[$i] = (count($r) > 1) ? array(trim($r[0]), trim($r[1])) : array(trim($r[0]));
        }
        
        // Apply specified checks.
        
        switch ($first_rule)
        {
            // Positive integer without leading zeroes.
            
            case 'int':
            case 'integer':
                
                // Basic checks.
                
                if (!ctype_digit($input)) return false;
                if ($input !== '0' && substr($input, 0, 1) === '0') return false;
                
                // Rule checks.
                
                foreach ($rules as $r)
                {
                    switch ($r[0])
                    {
                        case 'min': if ($input < $r[1]) return false; break;
                        case 'max': if ($input > $r[1]) return false; break;
                    }
                }
                return true;
            
            // IP address.
            
            case 'ip':
            
                // Basic checks.
                
                if (!filter_var($input, FILTER_VALIDATE_IP)) return false;
                
                // Rule checks.
                
                foreach ($rules as $r)
                {
                    switch ($r[0])
                    {
                        case 'ipv4': if (!filter_var($input, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV4))) return false; break;
                        case 'ipv6': if (!filter_var($input, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_IPV6))) return false; break;
                        case 'noprivate': if (!filter_var($input, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_NO_PRIV_RANGE))) return false; break;
                        case 'noreserved': if (!filter_var($input, FILTER_VALIDATE_IP, array('flags' => FILTER_FLAG_NO_RES_RANGE))) return false; break;
                    }
                }
                return true;
            
            // E-mail address.
            
            case 'email':
            case 'e-mail':
                
                // Basic checks.
                
                if (!filter_var($input, FILTER_VALIDATE_EMAIL)) return false;
                
                // Rule checks.
                
                foreach ($rules as $r)
                {
                    switch ($r[0])
                    {
                        case 'domain':
                        case 'host':
                            $domain = substr($input, strrpos($input, '@') + 1);
                            if ($domain != $r[1]) return false; break;
                    }
                }
                return true;
            
            // URL.
            
            case 'url':
            case 'uri':
            
                // Basic checks.
                
                if (!filter_var($input, FILTER_VALIDATE_URL)) return false;
                
                // Rule checks.
                
                foreach ($rules as $r)
                {
                    switch ($r[0])
                    {
                        case 'domain':
                        case 'host':
                            $domain = parse_url($input, PHP_URL_HOST);
                            if ($domain != $r[1]) return false; break;
                    }
                }
                return true;
            
            // Unicode string.
            
            case 'unicode':
            case 'utf-8':
            
                // Basic checks.
                
                if (!mb_check_encoding($input, 'UTF-8')) return false;
                
                // Rule checks.
                
                foreach ($rules as $r)
                {
                    switch ($r[0])
                    {
                        case 'length':
                        case 'len':
                            if (mb_strlen($input, 'UTF-8') != $r[1]) return false; break;
                            
                        case 'minlen':
                        case 'min':
                            if (mb_strlen($input, 'UTF-8') < $r[1]) return false; break;
                            
                        case 'maxlen':
                        case 'max':
                            if (mb_strlen($input, 'UTF-8') > $r[1]) return false; break;
                    }
                }
                return true;
            
            // String and its various subspecies.
            
            case 'string':
            case 'alpha':
            case 'alnum':
            case 'hex':
            
                // Basic checks.
                
                switch ($rule)
                {
                    case 'alpha': if (!ctype_alpha($input)) return false; break;
                    case 'alnum': if (!ctype_alnum($input)) return false; break;
                    case 'hex': if (!ctype_xdigit($input)) return false; break;
                }
                
                // Rule checks.
                
                foreach ($rules as $r)
                {
                    switch ($r[0])
                    {
                        case 'length':
                        case 'len':
                            if (strlen($input) != $r[1]) return false; break;
                            
                        case 'minlen':
                        case 'min':
                            if (strlen($input) < $r[1]) return false; break;
                            
                        case 'maxlen':
                        case 'max':
                            if (strlen($input) > $r[1]) return false; break;
                    }
                }
                return true;
            
            // Default returns false.
            
            default: return false;
        }
    }
    
    // Get a random string.
    
    public static function get_random($length)
    {
        // Use recursion if $length is greater than 64.
        
        if ($length > 128)
        {
            $random = '';
            while (strlen($random) < $length) $random .= self::get_random(128);
            return substr($random, 0, $length);
        }
        
        // Since we'll return a hex string, we need to collect ($length * 4) bits of entropy.
        
        $entropy_needed = ceil($length / 2);
        $entropy_obtained = microtime();
        
        // Attempt to open /dev/urandom, which is said to be better than PHP functions.
        
        if ($fp = @fopen('/dev/urandom', 'rb'))
        {
            $entropy_obtained .= "\n" . fread($fp, $entropy_needed);
            fclose($fp);
        }
        
        // On failure, just call mt_rand() which gives us ~31 bits of entropy each time.
        
        else
        {
            for ($i = 0; $i < $entropy_needed; $i += 3)
            {
                $entropy_obtained .= "\n" . mt_rand();
            }
        }
        
        // Hash and return.
        
        if ($length <= 40) return substr(hash('sha1', $entropy_obtained), 0, $length);
        if ($length <= 64) return substr(hash('sha256', $entropy_obtained), 0, $length);
        return substr(hash('sha512', $entropy_obtained), 0, $length);
    }
}