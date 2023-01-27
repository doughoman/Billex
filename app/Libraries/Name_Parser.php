<?php
if (!class_exists('Name_Parser')) {
    define('Name_Parser_VERSION', '1');

    class Name_Parser {

//Private properties
        public $type;
        public $name;
        public $title;
        public $first;
        public $middle;
        public $last;
        public $suffix;
        public $nickname;
        private $credentials;
        private $titles;
        private $prefixes;
        private $suffixes;

        public function __construct($name = NULL, $type = NULL) {
            $this->setName($name, $type);
        }

        public function setName($name = NULL, $type = NULL) {
            $this->name = "";
            $this->title = "";
            $this->first = "";
            $this->nickname = "";
            $this->middle = "";
            $this->last = "";
            $this->suffix = "";
            $this->type = $type;
            $this->sloppy = false;

            if ($name) {
                $this->name = $name;
                $this->credentials = array('md', 'do', 'pa', 'np');
                $this->titles = array('dr', 'miss', 'mr', 'mrs', 'ms', 'judge');
                $this->suffixes = array('esq', 'esquire', 'jr', 'sr', '2', 'ii', 'iii', 'iv');
                $this->prefixes = array('bar', 'bin', 'da', 'dal', 'de la', 'de', 'del', 'der', 'di',
                    'ibn', 'la', 'le', 'san', 'st', 'ste', 'van', 'van der', 'van den', 'vel', 'von');
                $this->slop = array(' and ', '&'); //need spaces in value if needed
                if ('last' == $type) {
                    $this->parse_last();
                } else {
                    $this->parse();
                }
            }
        }

        private function norm_str($string) {
            return trim(strtolower(
                            str_replace('.', '', $string)));
        }

        private function in_array_norm($needle, $haystack) {
            return in_array($this->norm_str($needle), $haystack);
        }

        private function arr_diff($subject, $remove) {
            foreach ($subject as $key => $val) {
                if ($this->in_array_norm($val, $remove)) {
                    unset($subject[$key]);
                }
            }
            return $subject;
        }

        private function arr_intersect($subject, $search) {
            $arr = array();
            foreach ($subject as $key => $val) {
                if ($this->in_array_norm($val, $search)) {
                    $arr[] = $val;
                }
            }
            return $arr;
        }

        private function clean_slop($string) {
            $string = ' ' . strtolower($string) . ' ';
            foreach ($this->slop as $slop) {
                if (strpos($string, $slop) !== false) {
                    $this->sloppy = true;
                }
                $string = str_replace($slop, ' ', $string);
            }
            return trim($string);
        }

        public function parse() {
            $name = $this->clean_slop($this->name);
            $pieces = explode(',', preg_replace('/\s+/', ' ', $name));
            $n_pieces = count($pieces);

            switch ($n_pieces) {
                case 1: // no comma in string, array(title first middles last suffix)
                    $subp = explode(' ', trim($pieces[0]));
                    //remove any credentials
                    $subp = array_values($this->arr_diff($subp, $this->credentials));

                    $n_subp = count($subp);
                    for ($i = 0; $i < $n_subp; $i++) {
                        $curr = trim($subp[$i]);

                        if ($i < ($n_subp - 1)) {
                            $next = trim($subp[$i + 1]);
                        } else {
                            $next = '';
                        }

                        if ($i == 0 && $this->in_array_norm($curr, $this->titles)) {
                            $this->title = $curr;
                            continue;
                        }

                        if (!$this->first) {
                            $this->first = $curr;
                            continue;
                        }

                        if ($i == $n_subp - 2 && $next && $this->in_array_norm($next, $this->suffixes)) {
                            if ($this->last) {
                                $this->last .= " $curr";
                            } else {
                                $this->last = $curr;
                            }
                            $this->suffix = $next;
                            break;
                        }

                        if ($i == $n_subp - 1) {
                            if ($this->last) {
                                $this->last .= " $curr";
                            } else {
                                $this->last = $curr;
                            }
                            continue;
                        }

                        if ($this->in_array_norm($curr, $this->prefixes)) {
                            if ($this->last) {
                                $this->last .= " $curr";
                            } else {
                                $this->last = $curr;
                            }
                            continue;
                        }

                        if ($next == 'y' || $next == 'Y') {
                            if ($this->last) {
                                $this->last .= " $curr";
                            } else {
                                $this->last = $curr;
                            }
                            continue;
                        }

                        if ($this->last) {
                            $this->last .= " $curr";
                            continue;
                        }

                        if ($this->middle) {
                            $this->middle .= " $curr";
                        } else {
                            $this->middle = $curr;
                        }
                    }
                    break;
                case 2:
                    //comma found so
                    //TODO - see if we find suffix or credential after the comma
                    switch ($this->in_array_norm($pieces[1], $this->suffixes)) {
                        case TRUE: // array(title first middles last,suffix(or credentials))
                            $subp = explode(' ', trim($pieces[0]));
                            $n_subp = count($subp);
                            for ($i = 0; $i < $n_subp; $i++) {
                                $curr = trim($subp[$i]);
                                if ($i < ($n_subp - 1)) {
                                    $next = trim($subp[$i + 1]);
                                } else {
                                    $next = '';
                                }


                                if ($i == 0 && $this->in_array_norm($curr, $this->titles)) {
                                    $this->title = $curr;
                                    continue;
                                }

                                if (!$this->first) {
                                    $this->first = $curr;
                                    continue;
                                }

                                if ($i == $n_subp - 1) {
                                    if ($this->last) {
                                        $this->last .= " $curr";
                                    } else {
                                        $this->last = $curr;
                                    }
                                    continue;
                                }

                                if ($this->in_array_norm($curr, $this->prefixes)) {
                                    if ($this->last) {
                                        $this->last .= " $curr";
                                    } else {
                                        $this->last = $curr;
                                    }
                                    continue;
                                }

                                if ($next == 'y' || $next == 'Y') {
                                    if ($this->last) {
                                        $this->last .= " $curr";
                                    } else {
                                        $this->last = $curr;
                                    }
                                    continue;
                                }

                                if ($this->last) {
                                    $this->last .= " $curr";
                                    continue;
                                }

                                if ($this->middle) {
                                    $this->middle .= " $curr";
                                } else {
                                    $this->middle = $curr;
                                }
                            }
                            $this->suffix = trim($pieces[1]);
                            break;
                        case FALSE: // array(last,title first middles suffix)
                            //TODO - need to handle last suffix, title first middle
                            $subp = explode(' ', trim($pieces[1]));
                            $subp = array_values($this->arr_diff($subp, $this->credentials));

                            $n_subp = count($subp);
                            for ($i = 0; $i < $n_subp; $i++) {
                                $curr = trim($subp[$i]);
                                $next = (isset($subp[$i + 1])) ? trim($subp[$i + 1]) : '';

                                if ($i == 0 && $this->in_array_norm($curr, $this->titles)) {
                                    $this->title = $curr;
                                    continue;
                                }

                                if (!$this->first) {
                                    $this->first = $curr;
                                    continue;
                                }

                                if ($i == $n_subp - 2 && $next &&
                                        $this->in_array_norm($next, $this->suffixes)
                                ) {
                                    if ($this->middle) {
                                        $this->middle .= " $curr";
                                    } else {
                                        $this->middle = $curr;
                                    }
                                    $this->suffix = $next;
                                    break;
                                }

                                if ($i == $n_subp - 1 && $this->in_array_norm($curr, $this->suffixes)) {
                                    $this->suffix = $curr;
                                    continue;
                                }

                                if ($this->middle) {
                                    $this->middle .= " $curr";
                                } else {
                                    $this->middle = $curr;
                                }
                            }
                            $subp = explode(' ', trim($pieces[0]));
                            $subp = array_values($this->arr_diff($subp, $this->credentials)); //remove credentials
                            if (!$this->suffix) {
                                $this->suffix = implode(' ', $this->arr_intersect($subp, $this->suffixes)); //if any matches to suffixes, use
                            }
                            $this->last = implode(" ", $this->arr_diff($subp, $this->suffixes)); //and then last name is everything else less suffixes and credentials
                            break;
                    }
                    unset($pieces);
                    break;
                case 3: // array(last,title first middles,suffix)
                    $subp = explode(' ', trim($pieces[1]));
                    $n_subp = count($subp);
                    for ($i = 0; $i < $n_subp; $i++) {
                        $curr = trim($subp[$i]);
                        //$next				=	trim($subp[$i+1]);
                        if ($i == 0 && $this->in_array_norm($curr, $this->titles)) {
                            $this->title = $curr;
                            continue;
                        }

                        if (!$this->first) {
                            $this->first = $curr;
                            continue;
                        }

                        if ($this->middle) {
                            $this->middle .= " $curr";
                        } else {
                            $this->middle = $curr;
                        }
                    }

                    $this->last = trim($pieces[0]);
                    $this->suffix = trim($pieces[2]);
                    break;
                default: // unparseable
                    unset($pieces);
                    break;
            }

            return true;
        }

        public function parse_last() {
            $pieces = explode(',', preg_replace('/\s+/', ' ', trim($this->name)));
            $n_pieces = count($pieces);

            switch ($n_pieces) {
                case 1: // array(last suffix)
                    $words = explode(' ', trim($pieces[0]));
                    foreach ($words as $word) {
                        if ($this->in_array_norm($word, $this->suffixes)) {
                            $this->suffix = $word;
                        } else {
                            if ($this->last) {
                                $this->last .= " $word";
                            } else {
                                $this->last = $word;
                            }
                        }
                    }
                    break;
                case 2:
                    if ($this->in_array_norm($pieces[1], $this->suffixes)) {
                        $this->suffix = trim($pieces[1]);
                        $this->last = trim($pieces[0]);
                    } else {
                        $words = explode(' ', trim($pieces[0]));
                        foreach ($words as $word) {
                            if ($this->in_array_norm($word, $this->suffixes)) {
                                $this->suffix = $word;
                            } else {
                                if ($this->last) {
                                    $this->last .= " $word";
                                } else {
                                    $this->last = $word;
                                }
                            }
                        }
                    }
                    unset($pieces);
                    break;
                case 3: // array(last,suffix,degree,suffix)
                    if ($this->in_array_norm($pieces[1], $this->suffixes)) {
                        $this->suffix = trim($pieces[1]);
                        $this->last = trim($pieces[0]);
                    } elseif ($this->in_array_norm($pieces[2], $this->suffixes)) {
                        $this->suffix = trim($pieces[2]);
                        $this->last = trim($pieces[0]);
                    } else {
                        $words = explode(' ', trim($pieces[0]));
                        foreach ($words as $word) {
                            if ($this->in_array_norm($word, $this->suffixes)) {
                                $this->suffix = $word;
                            } else {
                                if ($this->last) {
                                    $this->last .= " $word";
                                } else {
                                    $this->last = $word;
                                }
                            }
                        }
                    }
                    break;
                default: // unparseable
                    unset($pieces);
                    break;
            }

            return true;
        }

    }

}
//end name parser class
