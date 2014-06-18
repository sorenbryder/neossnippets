# Node date sorter

## How to do it

1. Copy SortByDateOperation.php into your own package.
2. Update namespace in php file

## How to use it

${q(site).children().sortByDate('date', 'ascending','upcoming')}

date: Date property defined in your content element in NodeTypes.yaml (See Configuration/NodeTypes.yaml for example)
ascending: Sort order. Leave blank for descending.
upcoming: Show only today and later. Remove to show all nodes.