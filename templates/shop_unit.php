<?php foreach ($goods as $good): ?>
<div class="shopUnit">
        <img src="<?php echo $good['img']; ?>" />

        <div class="shopUnitName">
           <?php echo $good['name']; ?>
        </div>
        <div class="shopUnitShortDesc">
            <?php echo substr($good['description'],0,60)."..."; ?>
        </div>
        
        <div class="shopUnitPriceViewsComments">
            <div id="shopUnitViews"> 
              <?php echo $good['views_count']; ?><span id="viewsIcon" class="material-icons">visibility</span>
            </div>
            
            <div id="shopUnitComments"> 
              <?php echo $good['comments_count']; ?><div id="commentIcon" class="material-icons">comment</div>
            </div>

            <div id="shopUnitRating"> 
              <?php echo $good['rating']; ?><div id="ratingIcon" class="material-icons">grade</div>
            </div>
          <div id="shopUnitPrice"><?php echo $good['price'] . '$'; ?></div>
        </div>

        <a href="shop.php?id=<?php echo $good['id']; ?>&comment_page=1" class="shopUnitMore">
            Подробнее
        </a>
    </div>
<?php endforeach; ?>