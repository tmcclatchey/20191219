SELECT
    COUNT(`userId`) AS `count`
FROM
    `user`
WHERE
    `roleId`=:roleId