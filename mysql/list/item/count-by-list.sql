SELECT
    COUNT(`itemId`) AS `count`
FROM
    `list_item`
WHERE
    `listId`=:listId