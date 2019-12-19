SELECT
    COUNT(`userId`) AS `count`
FROM
    `user`
WHERE
    `managerId`=:managerId