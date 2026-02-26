# BHL incoming links

Measuring the links to BHL from external sources such as Wikipedia, Catalogue of Life, etc.

## Initial experiments

To begin with using the Mediawiki API (see `wikipedia-api.php`) but this was time-consuming and I’m not convinced that the API gave me the results I needed. Hence I switched to using static dumps. For Wikispecies this is straightforward as it is a small wiki. For English language Wikipedia I use an SQL dum of external links.

## Wikispecies

Using the XML dump `specieswiki-20220701-pages-articles-multistream.xml` I extracted BHL URLs and then used Microcitation Parser to match URLs to BHL PageIDs.

The script `extract-from-specieswiki-dump.php` reads the Wikispecies XML dump and extracts URLs that link to BHL. It generates a tab-delimited list of Wikispecies pages, the page type, and the URL.

The script `bhl-link-to-page.php` reads the list of URLs and attemts to extract the BHl PageID, either from the URL, or by calling a local copy of my “Microcitation Parser” to convert an offset URL to a PageID.



### Problems parsing

Sometimes types had leading `/`:

```
SELECT * FROM specieswiki WHERE type LIKE "/%";
```

```
UPDATE specieswiki SET type = REPLACE(type,"/", "");
```

### Problems matching to BHL

In some cases microparser could not convert URLs into PageIDs. For example, an item was not retrieved from the BHl API (typically because it has been deleted).

#### item 89591 
Has been removed (was volume `142.d. (1999)` of *Tijdschrift voor entomologie*)

#### item 88509
Removed, not in BHL or BioStor. Not on current Wikispecies page for sources ([Sphaeriusidae](https://species.wikimedia.org/wiki/Sphaeriusidae),[Sphaerius](https://species.wikimedia.org/wiki/Sphaerius),[Sphaerius acaroides](https://species.wikimedia.org/wiki/Sphaerius), but in edit history for Sphaeriusidae https://species.wikimedia.org/w/index.php?title=Sphaeriusidae&diff=10583117&oldid=8832692 as 

> * {{aut|[[Waltl|Waltl, J.]]}} 1838: Beiträge zur nähem naturhistorischen Kenntniß des Unterdonaukreises in Bayern. ''Isis von Oken'', 1838: 250–273. [http://www.biodiversitylibrary.org/item/88509#6 BHL]

#### item 68510

Removed, was `9th ser. v. 11 (1923)` of  *Annals and magazine of natural history*. Link still on Wikispecies [Toxorhina levis](https://species.wikimedia.org/wiki/Toxorhina_levis) as:

> * {{aut|Alexander, C.P.}} 1923: New or little-known Tipulidae (Diptera). - XIII. Australasian Species. ''Annals and magazine of natural history'' (9), 11: 97–111. [http://www.biodiversitylibrary.org/item/68510#6 BHL] [https://web.archive.org/web/20110724201629/http://www.bugz.org.nz//WebForms/SearchForm.aspx BUGZ]
{{Edwards, 1923}}

#### item 41450

Removed, was item in [Analyse des familles des plantes, avec l'indication des principaux genres qui s'y rattachent](https://www.biodiversitylibrary.org/title/443) which has moved. No links with item so likely to be linked to a page that is also now missing from BHL.

#### item 25953

In BHL, not retrieved by Microparser (why?) Histoire naturelle des poissons t.18. No direct links, so likely a PageID in Wikispecies.

#### item 25911

In BHL, not retrieved by Microparser (why?) Histoire naturelle des poissons t.7. No direct links, so likely a PageID in Wikispecies.

## Wikipedia

Get SQL dump for links to external sources https://dumps.wikimedia.org/backup-index.html, we then need to process that table.

We parse the SQL and output links to BHL. The URLs are given in reverse, e.g. `http://org.biodiversitylibrary.www.`

Wikipedia pages are stored using integer ids for a given version, and can be viewed by setting `curid` to the value of `el_from`: e.g. https://en.wikipedia.org/?curid=20000357

Many pages with links are “Talk” pages rather than articles.

## Wikidata

To do.

## Catalogue of Life and other taxonomic databases





