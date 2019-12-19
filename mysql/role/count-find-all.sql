SELECT
    COUNT(`roleId`) AS `count`
FROM
    `role`
WHERE
    (
        `roleName` LIKE CONCAT('%', :query, '%') OR
        `roleDescription` LIKE CONCAT('%', :query, '%')
    )