define('GITBLOG_DIR', dirname(realpath(__FILE__)));
$conf = json_decode(file_get_contents(GITBLOG_DIR.'/config.json'), true);
	$p = strrpos($path, '.', strrpos($path, '/'));
	return $p > 0 ? substr($path, $p+1) : '';
# path w/o extension
function filenoext($path) {
	$p = strpos($path, '.', strrpos($path, '/'));
	return $p > 0 ? substr($path, 0, $p) : $path;
}
function gb_atomic_write($filename, &$data) {
	$tempnam = tempnam(dirname($filename), basename($filename));
	$f = fopen($tempnam, 'w');
	fwrite($f, $data);
	fclose($f);
	if (!rename($tempnam, $filename)) {
		unlink($tempnam);
		return false;
	}
	return true;
}

	public $previousFiles; # available for GitPatch::RENAME and COPY. array('file1', 'file2', ..)
			'detectRC' => true,
			'mapnamestoc' => false # if true, returns a 3rd arg: map[name] => commit
		$ntoc = $kwargs['mapnamestoc'] ? array() : null;
				$t = $line[0]{0};
				$previousName = null;
				
				# R|C have two names wherether the last is the new name
				if ($t === GitPatch::RENAME or $t === GitPatch::COPY) {
				  $previousName = $name;
				  $name = $line[2];
				  if ($c->previousFiles === null)
				    $c->previousFiles = array($previousName);
				  else
				    $c->previousFiles[] = $previousName;
			  }
				
				# add to files[tag] => [name, ..]
				
			  # if kwarg mapnamestoc == true
			  if ($ntoc !== null) {
			    if (!isset($ntoc[$name]))
			      $ntoc[$name] = array($c);
			    else
			      $ntoc[$name][] = $c;
		    }
		    
			  # update cached objects
				
				# update existing
				# todo: make it work with $rcron -- currently relies on a cronol. sequence. Idea: post-process (slow but robust)
					if ($t === GitPatch::CREATE or $t === GitPatch::COPY)
					elseif ($t === GitPatch::RENAME) {
					  if (isset($existing[$previousName])) {
					    # move original CREATE
					    $existing[$name] = $existing[$previousName];
					    unset($existing[$previousName]);
				    }
				    else {
  					  $existing[$name] = $c;
  				  }
  				  # move commits from previous file if kwarg mapnamestoc == true
  				  if ($ntoc !== null and isset($ntoc[$previousName])) {
  				    $ntoc[$name] = array_merge($ntoc[$previousName], $ntoc[$name]);
  				    unset($ntoc[$previousName]);
				    }
				  }
		return array($commits, $existing, $ntoc);
	function init($shared=true, $bare=false) {
		$mkdirmode = 0755;
		if ($shared) {
			$shared = 'true';
			$mkdirmode = 0775;
		}
		else
			$shared = 'false';
		$cmd = "init --quiet --shared=$shared";
		
		if (!$bare) {
			$dirname = dirname($this->gitdir);
			mkdir("$dirname/content/posts", $mkdirmode, true);
			mkdir("$dirname/content/pages", $mkdirmode);
		}
		
		$skeleton = dirname(realpath(__FILE__)).'/skeleton';
		copy("$skeleton/hooks/post-commit", "{$this->gitdir}/hooks/post-commit");
		
	const COPY = 'C';
	const RENAME = 'R';
				$currpatch->action = self::COPY;
				$currpatch->action = self::RENAME;
			'tags' => array(),
			'published' => 0
			
			# tags
				$nmeta['tags'] = preg_split('/[, ]+/', $nmeta['tags']);
			}
			
			# published
			# todo: translate these rules to english:
			#- Om headern saknas defaultar den till true.
      #- Om published värde inte kan parsas av strtotime (typ om man anger 
      #  2102-01-01 (out of bounds) eller "mosmaster") tolkas det som false.
      #- Om headern tolkas som true (el. implicit saknas) används datumet från
      #  den commit som skapade filen (genom create eller copy).
			if (isset($nmeta['published'])) {
				$lc = strtolower($nmeta['published']);
				if ($lc) {
					if ($lc{0} === 't' || $lc{0} === 'y')
						$nmeta['published'] = 0;
					elseif ($lc{0} === 'f' || $lc{0} === 'n')
						$nmeta['published'] = 2147483647; # MAX (Jan 19, 2038)
					else {
						$ts = strtotime($nmeta['published']);
						if ($ts === false or $ts === -1) # false in PHP >=5.1.0, -1 in PHP <5.1.0
						  $ts = 2147483647; # MAX (Jan 19, 2038)
						$nmeta['published'] = $ts;
					}
				}
			
	
	/** todo: comment */
	static function contentForName($repo, $name) {
		$path = "{$repo->gitdir}/info/gitblog/stage/$name";
		$data = @file_get_contents($path);
		if ($data === false)
			if (GitObjectIndex::assureIntegrity($repo))
				$data = file_get_contents($path);
		if ($data === false)
			return null;
		return unserialize($data);
	}
	
	/** todo: comment */
	static function postForSlug($repo, $slug, $type='html') {
		return self::contentForName($repo, "content/posts/$slug.$type");
	}
	
	static function publishedPosts($repo, $limit=25) {
		$records = GitObjectIndex::tail($repo, 'stage-published-posts.rchlog', $limit);
		$posts = array();
		
		foreach ($records as $rec) {
		  $name =& $rec['name'];
			$post = self::contentForName($repo, "content/posts/$name");
			$post->slug = filenoext($name);
			$posts[] = $post;
		}
		
		return $posts;
	}
	const STAGE_DIRMODE = 0775;
	static function write($repo, $indexname, &$data, $mode) {
		$filename = "{$repo->gitdir}/info/gitblog/index/{$indexname}";
		if (gb_atomic_write($filename, $data)) {
			chmod($filename, $mode);
			return true;
		}
		return false;
	static function writeContentObjectToStage($stagedir, $object, $commits, &$meta, &$body) {
			if ($ccommit === null) {
			  if (
			    (isset($c->files[GitPatch::CREATE]) and in_array($object->name, $c->files[GitPatch::CREATE], true))
			    or
			    (isset($c->files[GitPatch::RENAME]) and in_array($object->name[1], $c->files[GitPatch::RENAME], true))
			  ) {
				  $ccommit = $co;
			  }
		$acommits = array_values($acommits);
		if ($ccommit === null and $acommits)
		  $ccommit = $acommits[0];
			'meta' => (object)$meta,
			'body' => $body,
		$filename = "{$stagedir}/{$object->name}";
		$bw = file_put_contents($filename, $data, LOCK_EX);
		chmod($filename, 0664);
		$v = GitCommit::find($repo, array('sortrcron' => false, 'mapnamestoc' => true));
		$name_to_commits =& $v[2];
		$newstagedir = $stagedir = "{$repo->gitdir}/info/gitblog/stage.new";
		/*$name_to_commits = array();
				  if ($t === GitPatch::RENAME or $t === GitPatch::COPY) {
				    foreach ($name as $n) {
    					if (!isset($name_to_commits[$n]))
    						$name_to_commits[$n] = array($c);
    					else
    						$name_to_commits[$n][] = $c;
  					}
					}
					else {
  					if (!isset($name_to_commits[$name]))
  						$name_to_commits[$name] = array($c);
  					else
  						$name_to_commits[$name][] = $c;
					}
		}*/
			$published = $c->comitterDate;
			# content
			if (substr($name, 0, 8) === 'content/') {
				$data = $file->data;
				$mb = GitContent::parseData($data);
				$meta =& $mb[0];
				$body =& $mb[1];
				$published = $meta['published'];
				if ($published === 0) {
					$published = $c->comitterDate;
					$meta['published'] = $published;
				}
				
				self::writeContentObjectToStage($newstagedir, $file, $name_to_commits[$name], $meta, $body);
			}
			# stage
			$stage[$file->id] = array($published, $c->id, $file->id, $name);
		$stagedat = serialize($stage);
		self::write($repo, 'stage.phpser', $stagedat, 0664);
		self::write($repo, 'stage.rchlog', $stage_rcron, 0664);
		
		self::buildPublishedPostsRCHLog($repo, $stage);
	static function buildPublishedPostsRCHLog($repo, &$stage) {
		# build published posts rchlog
		# reverse-chronologically ordered list of published content/posts/**
		$data = '';
		$now = time()+300; # 5 minutes granularity
		$sorted = array();
		
		$i = 0;
		foreach ($stage as $o)
			if ( (substr($o[3], 0, 14) === 'content/posts/') and ($o[0] <= $now) )
				$sorted[strval($o[0]).strval($i++)] = $o;
		
		ksort($sorted, SORT_NUMERIC);
		
		foreach ($sorted as $o)
			$data .= self::encodeRec($o[0], $o[1], $o[2], substr($o[3], 14));
		
		self::write($repo, 'stage-published-posts.rchlog', $data, 0664);
	}
	
	static function activateStage($repo, $newstagedir) {
		$stagedir = "{$repo->gitdir}/info/gitblog/stage";
		$intermediatestagedir = "{$stagedir}.old";
		
		if (!file_exists($stagedir))
			return rename($newstagedir, $stagedir);
		
		if (rename($stagedir, $intermediatestagedir)) {
			if (rename($newstagedir, $stagedir)) {
				exec("rm -rf ".escapeshellarg($intermediatestagedir));
				return true;
			}
			else {
				if (rename($intermediatestagedir, $stagedir))
					trigger_error("failed to rename $newstagedir => $stagedir -- old stage still active");
				else
					trigger_error("failed to rename $newstagedir => $stagedir -- CRITICAL: no active stage!");
			}
		}
		else {
			trigger_error("failed to rename $stagedir => $intermediatestagedir -- old stage still active");
		}
		return false;
	}
	
	
	# returns true if the integrity was compromised and has been repaired.
	static function assureIntegrity($repo) {
		$path = "{$repo->gitdir}/info/gitblog";
		if (is_dir($path))
			return false;
		# no gb meta dir!
		# do we even have a repo?
		$repo_exists = is_dir($repo->gitdir);
		if (!$repo_exists)
			if (!$repo->init())
				return false;
		self::rebuild($repo);
	}
$repo = new GitRepository(dirname(GITBLOG_DIR)."/db/.git");