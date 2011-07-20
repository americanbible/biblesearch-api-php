<body>

<ul>
  <li><a href="?action=version">Test Version</a></li>
  <li><a href="?action=book">Test Book</a></li>
</ul>

<?php
    include('../Version.php');
    include('../Book.php');
    include('../Chapter.php');
    include('../Verse.php');
    include('../Bookgroup.php');
    include('../Iterator.php');
    include('../User.php');
    include('../Note.php');
    include('../Tag.php');
    include('../Tagging.php');
   
    // create new api object
    $api = new ABS_Api( 'ga5YyPm5MkI3FGqEjMRJVfAVHLtEYTmfrXX7mQCN' );
    
    $actions = array();
    if (isset($_GET['action'])) {
         $actions = explode(',',$_GET['action']);
    }
    
    if (in_array('version',$actions)) {
         // Versions examples
        echo '<p>Versions</p>';
        $v = new ABS_Version($api);
        $xml = $v->listVersions();
        pr($xml);
        $v->setVersion('GNT');
        echo '<p>Version Books</p>';
        $xml = $v->books();
        pr($xml);
        echo '<p>Version Details</p>';
        $xml = $v->show();
        pr($xml);
    }
   
    if (in_array('book',$actions)) {
        // Books
        echo '<p>Books</p>';
        $book = new ABS_Book($api,'GNT');
        $xml = $book->listBooks();
        pr($xml);
        echo '<p>Book Details</p>';
        $xml = $book->show('Acts');
        pr($xml);
        echo '<p>Book Group 1</p>';
        $xml = $book->showBookGroup(1);
        pr($xml);
    }
    
    if (in_array('search',$actions)) {
        $v = new ABS_Verse($api);
        echo '<p>Search for "love", limit 10 results, KJV and GNT</p>';
        
        $xml = $v->search(array('keyword'   => 'love',
                                'limit'     => 10,
                                'version'  => 'KVJ,GNT'
                                ));
        pr($xml);
        $i = new ABS_Iterator($v);
        echo '<ul>';
        foreach ($i as $verse) {
            echo '<li>';
            echo $verse->reference;
            echo '</li>';
        }
        echo '</ul>';
    }
    
    if (in_array('chapter',$actions)) {
        $c = new ABS_Chapter($api,'GNT','Acts');
        $xml = $c->listChapters();
        echo '<p>Chapters</p>';
        pr($xml);
        echo '<p>Chapter Details</p>';
        $xml = $c->show(1);
        pr($xml);
        echo '<p>Chapter Verses</p>';
        $xml = $c->verses(1);
        pr($xml);
        echo '<p>Three verses</p>';
        $xml = $c->references(1,1,3);
        pr($xml);
    }
    
    if (in_array('verse',$actions)) {
        $verse = new ABS_Verse($api);
        $verse->setVersion('GNT');
        $verse->setBook('Acts');
        $verse->setChapter(1);
        $xml = $verse->listVerses();
        
        echo '<p>Verses</p>';
        pr($xml);
        $xml = $verse->show(1);
        echo '<p>Details</p>';
        pr($xml);
        $verse->setVersion('GNT,KJV');
        echo '<p>Passages 1 John 1:1-1,Romans 2:1-5</p>';
        $xml = $verse->passages('1 John 1:1-1,Romans 2:1-5');
        pr($xml);
        $xml = $verse->getData();
        pr($xml);
    }
    if (in_array('bookgroup',$actions)) {
        $bg = new ABS_Bookgroup($api);
        
        $xml = $bg->listBookGroups();
        
        echo '<p>All Book Groups - GNT</p>';
        pr($xml);
        $xml = $bg->show(1);
        echo '<p>Group details</p>';
        pr($xml);
    }
    if (in_array('user',$actions)) {
        $user = new ABS_User($api);
        $xml = $user->getUser();
        pr($xml);
        $xml = '<user id="2542"><first_name>Timm</first_name></user>';
        $r = $user->updateUser($xml);
        pr($r);
    }
    if (in_array('note',$actions)) {
        $note = new ABS_Note($api);
        echo '<p>List Notes</p>';
        $xml = $note->listNotes();
        $note->setThrowOnFail(false);
        pr($xml);
        echo '<p>Show a Note</p>';
        try {
            $xml = $note->show(367);
            pr($xml);
        } catch(Exception $e) {
            print $e->getMessage();
        }
    }
    
    if (in_array('tag',$actions)) {
        $t = new ABS_Tag($api);
        echo '<p>All Tags</p>';
        try {
            $xml = $t->listTags();
            pr($xml);
            $i = new ABS_Iterator($t);
            foreach ($i as $tag) {
                echo $tag->name;
                echo '<br />';
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        echo '<p>All User Tags</p>';
        try {
            $xml = $t->listUserTags();
            pr($xml);
            $i = new ABS_Iterator($t);
            foreach ($i as $tag) {
                echo $tag->name;
                echo '<br />';
            }
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        echo '<p>Show Tag by ID</p>';
        $xml = $t->show(1);
        pr($xml);
        echo '<p>Show Tag by Name</p>';
        $xml = $t->showByName('love');
        pr($xml);
        echo '<p>Get references used by an iterator</p>';
        $data = $t->getData();
        pr($data);
    }
    if (in_array('tagging',$actions)) {
        $tagging = new ABS_Tagging($api);
        $tag = '<tagging>
  <tag>
    <name>peace</name>
  </tag>      
  <references>
    <reference>
      <start>GNT:John.3.18</start>
      <end>GNT:John.3.18</end>
    </reference>
  </references>
</tagging>';
        //$xml = $tagging->addTag($tag);
        //pr($xml);
        echo '<p>All user taggings</p>';
        $xml = $tagging->listTaggings();
        pr($xml);
        //echo '<p>Update a tag</p>';
        $tag = '<tagging>
  <tag>
    <name>peace</name>
  </tag>      
  <references>
    <reference>
      <start>GNT:John.3.17</start>
      <end>GNT:John.3.19</end>
    </reference>
  </references>
</tagging>';
        //$xml = $tagging->updateTag(818,$tag);
        //pr($xml);
        //echo '<p>Delete a Tag</p>';
        //$tagging->deleteTag(819);
    }
    exit;

function pr($data) {
    echo '<pre>';print_r($data);echo '</pre>';
}
?>

</body>