<h1>
    Каталог товаров
</h1>
<div>
    <?php foreach ($goods as $good): ?>
    <div class="shopUnit">
        <img src="<?php echo $good['img']; ?>" />

        <div class="shopUnitName">
           <?php echo $good['name']; ?>
        </div>
        <div class="shopUnitShortDesc">
            <?php echo $good['description']; ?>
        </div>
        <div class="shopUnitPrice">
           <?php echo $good['price'] . '$'; ?>
        </div>
        <a href="index.php?page=product&id=<?php echo $good['id']; ?>" class="shopUnitMore">
            Подробнее
        </a>
    </div>
    <?php endforeach; ?>
</div>
