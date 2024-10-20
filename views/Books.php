<ul>
  <?php foreach ($data['books'] as $book) : ?>
    <li>
      <a
        href="<?php echo BASE_URL . $data['version'] . '/' . $book['sigla']; ?>">
        <?php echo $book['nome']; ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>

<style>
  ul {
    list-style-type: none;
    padding: 0;
  }

  li {
    padding: 10px;
    border-bottom: 1px solid #ccc;
  }

  li:hover {
    background-color: #f9f9f9;
  }
</style>