Инструкция
----------

Требования:
  * установленный php ^7.0
  * установленный mysql ^5.6
  * заполнить настройки доступа к бд в `bootstrap/config.php`
  
Запуск в консоли:
  * сгенерировать дерево `php ./generate.php`
  * получить список дочерних узлов `php ./childen.php 123`, где 123 - id узла
  * получить список предков узла `php ./ancestors.php 123`, где 123 - id узла
  * переместить узел `php ./move.php id=123 parent_id=456 position=1`, где 123 -
  id перемещаемого узла, parent_id - id узла к которому нужно привязать
  перемещаемый узел, position - позиция (слева=1, справа=2)
