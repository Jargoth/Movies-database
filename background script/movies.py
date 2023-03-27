import pg
import urllib
import re
import string

database_name = 'jargoth'
database_adress = '192.168.1.2'
database_username = ''
database_password = ''

def getMovieInfo():
    #reguljärt uttryck för betyget
    pat = "<b>\d.\d/10</b>"
    patGrade = re.compile(pat)
        
    #reguljärt uttryck för utgivningsåret
    pat = " \(\d*[\WI]*\)\s?\(?m?i?n?i?T?V?\)?</b>"
    patYear = re.compile(pat)
    
    #reguljärt uttryck för genren
    pat = "<a href=\"/Sections/Genres/\w*/\">\w*</a>"
    patGenre = re.compile(pat)
    
    #reguljärt uttryck för keyword
    pat = "<a href=\"/keyword/\S*/\">\S*</a>"
    patKeyword = re.compile(pat)
    
    #reguljärt uttryck för antal röster
    pat = "<a href=\"ratings\">\d*,?\d* votes</a>"
    patNumberOfVotes = re.compile(pat)
    
    #reguljärt uttryck för regisörerna
    pat = "<h5>Director:</h5>\s*<a href=\"/name/nm\d*/\">[\S ]*</a><br/>"
    patDirector = re.compile(pat)
    
    #reguljärt uttryck för manusförfattarna
    pat = '<h5><a class="glossary" name="writers" href="/Glossary/W#writer">Writing credits</a></h5>.*</td></tr></table><?b?r?>? <h5><a class="glossary" href="/Glossary/C#cast"> Cast</a>'
    patWriter = re.compile(pat)
    
    #reguljärt uttryck för plot summary
    pat = '<p class="plotpar">\n*.*\n*<i>\n* Written by\n* <a href="/SearchPlotWriters?.*</a>.*\n*</i>'
    patPlotSummary = re.compile(pat)
    
    #reguljärt uttryck för cast
    pat = '<a name="cast" class="glossary" href="/Glossary/C#cast"> Cast[\s\S]*?<a class="glossary" '
    patCast = re.compile(pat)
    
    #reguljärt uttryck för producers
    pat = '(<a class="glossary" name="producers" href="/Glossary/P#producer">Produced by[\s\S]*?((<a class="glossary" )|(<div)))'
    patProducer = re.compile(pat)
    
##    #reguljärt uttryck för cast
##    pat = '<a class="glossary" href="/Glossary/C#cast"> Cast[\s\S]*?<?a? ?c?l?a?s?s?=?"?g?l?o?s?s?a?r?y?"? ?<?d?i?v? ?a?l?i?g?n?=?"?c?e?n?t?e?r?"?>?'
##    patCast = re.compile(pat)
    
    #skapar anslutningen till databasen
    db = pg.connect(dbname = database_name, host = database_adress, user = database_username, passwd = database_password)
    
    #kollar vilka filmer som infon behöver uppdateras till
    SQL = "SELECT imdb, id, title FROM movies.movie WHERE (extract('day' FROM age(edited)) > 7 OR edited IS NULL) AND imdb IS NOT NULL LIMIT 10"
##    SQL = "SELECT imdb, id, title FROM movies.movie WHERE imdb IS NOT NULL AND id = 562 ORDER BY id LIMIT 1"
##    SQL = "SELECT imdb, id, title FROM movies.movie WHERE imdb IS NOT NULL ORDER BY id"
    movies = db.query(SQL).dictresult()
    
    #för alla filmer som hittades i frågan
    for movie in movies:
        print str(movie['id']) + ": " + movie['title'] + " " + movie['imdb']
        
        #öppnar sidan till filmen och läser av huvudsidan
        fp = urllib.urlopen(movie['imdb'])
        mainPage = fp.read()
        fp.close()
        
        #öppnar keywordssidan
        fp = urllib.urlopen(movie['imdb'] + "keywords")
        keywordsPage = fp.read()
        fp.close()
        
        #öppnar creditssidan
        fp = urllib.urlopen(movie['imdb'] + "fullcredits")
        creditsPage = fp.read()
        fp.close()
        
        #öppnar plotsummary
        fp = urllib.urlopen(movie['imdb'] + 'plotsummary')
        plotSummaryPage = fp.read()
        fp.close()
        
        #läser av betyg och lagrar i variabeln grade
        grade = re.findall(patGrade, mainPage)
        try:
            grade = string.split(grade[0][3:], '/')[0]
        except:
            grade = '0'
##        print "grade: " + grade
        
        #läser av antal röster
        votes = re.findall(patNumberOfVotes, mainPage)
        try:
            votes = string.split(votes[0][18:-10], ',')
            if len(votes) > 1:
                votes = 1000 * int(votes[0]) + int(votes[1])
            else:
                votes = int(votes[0])
        except:
            votes = 0
            
##        print "votes: " + str(votes)
        
        #läser av utgivningsår och lagrar i variablen year
        year = re.findall(patYear, mainPage)
        year = year[0][2:6]
        
##        print "year: " + year
        
        #läser av genrarna
        genres = re.findall(patGenre, mainPage)
##        print "genres"
        for genre in genres:
            genre = string.split(genre[:-4], ">")[-1]
            
##            print genre
            
            #kollar om genrern redan finns, annars läggs den till
            SQL = "SELECT id FROM movies.genre WHERE title = '" + pg.escape_string(genre) + "'"
            SQL = SQL.decode('iso8859-1').encode('utf-8')
            res = db.query(SQL).dictresult()
            try:
                res[0]['id']
                genreID = res[0]['id']
            except:
                SQL = "INSERT INTO movies.genre (title) VALUES ('" + pg.escape_string(genre) + "')"
                SQL = SQL.decode('iso8859-1').encode('utf-8')
                db.query(SQL)
                SQL = "SELECT currval('movies.genre_id_seq')"
                genreID = db.query(SQL).dictresult()[0]['currval']
            SQL = "SELECT * FROM movies.genre_movie WHERE genre = " + str(genreID) + " AND movie = " + str(movie['id'])
            try:
                db.query(SQL).dictresult()[0]['genre']
            except:
                SQL = "INSERT INTO movies.genre_movie (genre, movie) VALUES (" + str(genreID) + ", " + str(movie['id']) + ")"
                db.query(SQL)
        
        #läser av keywords
        keywords = re.findall(patKeyword, keywordsPage)
##        print "keywords"
        for keyword in keywords:
            keyword = string.split(keyword[:-4], ">")[-1]
            
##            print keyword
            
            #kollar om keywordet redan finns, annars läggs den till
            SQL = "SELECT id FROM movies.keyword WHERE title = '" + pg.escape_string(keyword) + "'"
            SQL = SQL.decode('iso8859-1').encode('utf-8')
            res = db.query(SQL).dictresult()
            try:
                res[0]['id']
                keywordID = res[0]['id']
            except:
                SQL = "INSERT INTO movies.keyword (title) VALUES ('" + pg.escape_string(keyword) + "')"
                SQL = SQL.decode('iso8859-1').encode('utf-8')
                db.query(SQL)
                SQL = "SELECT currval('movies.keyword_id_seq')"
                keywordID = db.query(SQL).dictresult()[0]['currval']
            SQL = "SELECT * FROM movies.keyword_movie WHERE keyword = " + str(keywordID) + " AND movie = " + str(movie['id'])
            try:
                db.query(SQL).dictresult()[0]['keyword']
            except:
                SQL = "INSERT INTO movies.keyword_movie (keyword, movie) VALUES (" + str(keywordID) + ", " + str(movie['id']) + ")"
                db.query(SQL)
                
        #läser av regisörerna
        directors = re.findall(patDirector, mainPage)
##        print directors
        if directors:
            directors = string.split(directors[0][28:-9], '</a><br/>')
            for director in directors:
                director = string.split(director, '">')
                director[0] = "http://www.imdb.com" + director[0]
                
##                print "director: " + director
                
                #kollar om regisören redan finns, annars läggs den till
                SQL = "SELECT id FROM movies.person WHERE adress = '" + pg.escape_string(director[0]) + "'"
                SQL = SQL.decode('iso8859-1').encode('utf-8')
                res = db.query(SQL).dictresult()
                try:
                    res[0]['id']
                    directorID = res[0]['id']
                except:
                    SQL = "INSERT INTO movies.person (adress, name) VALUES ('" + pg.escape_string(director[0]) + "', '" + pg.escape_string(director[1]) + "')"
                    SQL = SQL.decode('iso8859-1').encode('utf-8')
                    db.query(SQL)
                    SQL = "SELECT currval('movies.person_id_seq')"
                    directorID = db.query(SQL).dictresult()[0]['currval']
                SQL = "SELECT * FROM movies.person_movie WHERE type = 1 AND person = " + str(directorID) + " AND movie = " + str(movie['id'])
                try:
                    db.query(SQL).dictresult()[0]['person']
                except:
                    SQL = "SELECT nextval('movies.nothing')"
                    nothing = db.query(SQL).dictresult()[0]['nextval']
                    SQL = "INSERT INTO movies.person_movie (type, person, movie, description) VALUES (1, " + str(directorID) + ", " + str(movie['id']) + ", 'nothing" + str(nothing) + "')"
                    db.query(SQL)
                
        #läser av författarna
        writers = re.findall(patWriter, creditsPage)
        if writers:
##            print "writers"
            writers = string.replace(writers[0], ' (in alphabetical order) ', '')
            writers = string.replace(writers, '<small> (<a href="/wga">WGA</a>)</small>', '')
            writers = string.replace(writers, '<br><br></td></tr></table> <h5><a class="glossary" href="/Glossary/C#cast"> Cast</a>', '')
            writers = string.replace(writers, '</td><td>&nbsp;</td><td valign="top"></td></tr></table><br> <h5><a class="glossary" href="/Glossary/C#cast"> Cast</a>', '')
            writers = writers[120:]
            writers = string.split(writers, '</td></tr><tr><td valign="top">')
            for writer in writers:
                writer = string.split(writer[9:], '">', 1)
                writer[0] = 'http://www.imdb.com' + writer[0]
                writer[1] = string.split(writer[1], '</a></td><td>&nbsp;</td><td valign="top">')
                writer[1][0] = string.replace(writer[1][0], '</a>', '')
                try:
                    writer[1][1] = string.replace(writer[1][1], '<br><br>', '')
                    writer[1][1] = string.replace(writer[1][1], ' and ', '')
                    writer[1][1] = string.replace(writer[1][1], ' &amp; ', '')
                    writer[1][1] = string.replace(writer[1][1], '</td></tr></table><br> <h5><a class="glossary" href="/Glossary/C#cast"> Cast</a>', '')
                except:
                    pass
                    
##                print writer[1][0]
                    
                #kollar om författaren redan finns, annars läggs den till
                SQL = "SELECT id FROM movies.person WHERE adress = '" + pg.escape_string(writer[0]) + "'"
                SQL = SQL.decode('iso8859-1').encode('utf-8')
                res = db.query(SQL).dictresult()
                try:
                    res[0]['id']
                    writerID = res[0]['id']
                except:
                    SQL = "INSERT INTO movies.person (adress, name) VALUES ('" + pg.escape_string(writer[0]) + "', '" + pg.escape_string(writer[1][0]) + "')"
                    SQL = SQL.decode('iso8859-1').encode('utf-8')
                    db.query(SQL)
                    SQL = "SELECT currval('movies.person_id_seq')"
                    writerID = db.query(SQL).dictresult()[0]['currval']
                SQL = "SELECT * FROM movies.person_movie WHERE type = 2 AND person = " + str(writerID) + " AND movie = " + str(movie['id'])
                try:
                    db.query(SQL).dictresult()[0]['person']
                except:
                    if len(writer[1]) == 1:
                        SQL = "SELECT nextval('movies.nothing')"
                        nothing = db.query(SQL).dictresult()[0]['nextval']
                        SQL = "INSERT INTO movies.person_movie (type, person, movie, description) VALUES (2, " + str(writerID) + ", " + str(movie['id']) + ", 'nothing" + str(nothing) + "')"
                    if len(writer[1]) == 2:
                        if writer[1][1] == '':
                            SQL = "SELECT nextval('movies.nothing')"
                            nothing = db.query(SQL).dictresult()[0]['nextval']
                            SQL = "INSERT INTO movies.person_movie (type, person, movie, description) VALUES (2, " + str(writerID) + ", " + str(movie['id']) + ", 'nothing" + str(nothing) + "')"
                        else:
                            SQL = "INSERT INTO movies.person_movie (type, person, movie, description) VALUES (2, " + str(writerID) + ", " + str(movie['id']) + ", '" + pg.escape_string(writer[1][1]) + "')"
                            SQL = SQL.decode('iso8859-1').encode('utf-8')
                    db.query(SQL)
                
        #läser plot summary
        plotSummaries = re.findall(patPlotSummary, plotSummaryPage)
        for plotSummary in plotSummaries:
            plotSummary = string.split(plotSummary[20:-9], '\n<i>\n Written by\n <a href="/SearchPlotWriters?')
            plotSummary[1] = string.split(string.split(plotSummary[1], '">')[1], ' {')
            try:
                plotSummary[1][1] = string.split(plotSummary[1][1], '}')[0]
            except:
                pass
            if len(plotSummary[1]) >= 2:
                SQL = "SELECT id FROM movies.\"user\" WHERE name = '" + pg.escape_string(plotSummary[1][0]) + "' AND mejl = '" + pg.escape_string(plotSummary[1][1]) + "'"
            if len(plotSummary[1]) == 1:
                SQL = "SELECT id FROM movies.\"user\" WHERE name = '" + pg.escape_string(plotSummary[1][0]) + "'"
            SQL = SQL.decode('iso8859-1').encode('utf-8')
            res = db.query(SQL).dictresult()
            try:
                userID = res[0]['id']
            except:
                if len(plotSummary[1]) == 1:
                    SQL = "INSERT INTO movies.\"user\" (name) VALUES ('" + pg.escape_string(plotSummary[1][0]) + "')"
                if len(plotSummary[1]) >= 2:
                    SQL = "INSERT INTO movies.\"user\" (name, mejl) VALUES ('" + pg.escape_string(plotSummary[1][0]) + "', '" + pg.escape_string(plotSummary[1][1]) + "')"
                SQL = SQL.decode('iso8859-1').encode('utf-8')
                db.query(SQL)
                SQL = "SELECT currval('movies.user_id_seq')"
                userID = db.query(SQL).dictresult()[0]['currval']
            SQL = "SELECT movie FROM movies.\"plotSummary\" WHERE content = '" + pg.escape_string(plotSummary[0]) + "' AND \"user\" = " + str(userID) + " AND movie = " + str(movie['id'])
            SQL = SQL.decode('iso8859-1').encode('utf-8')
            res = db.query(SQL).dictresult()
            try:
                res[0]['movie']
            except:
                SQL = "INSERT INTO movies.\"plotSummary\" (content, \"user\", movie) VALUES ('" + pg.escape_string(plotSummary[0]) + "', " + str(userID) + ", " + str(movie['id']) + ")"
                SQL = SQL.decode('iso8859-1').encode('utf-8')
                db.query(SQL)
                
        #läser cast
        cast = re.findall(patCast, creditsPage)
##        print cast
        if cast:
##            print "cast"
            cast[0] = string.replace(cast[0], 'name="cast" ', '')
            cast = cast[0][53:-131]
            cast = string.split(cast, 'rest of cast listed alphabetically:</small></td></tr>')
            cast[0] = string.replace(cast[0], '<span> (in credits order) <small>verified as complete</small></span></h5><table class="cast">  ', '')
            cast[0] = string.replace(cast[0], '<span> (in credits order) <small></small></span></h5><table class="cast">  ', '')
            cast[0] = string.replace(cast[0], '</td></tr> <tr><td align="center" colspan="4"><small>', '')
            cast[0] = string.split(cast[0], '</table><div><form action="/character/create"')[0]
            cast[0] = string.split(cast[0], '<tr')
            cast[0] = cast[0][1:]
            i = 0
            for actor in cast[0]:
                i = i + 1
                actor = string.replace(actor, ' class="odd"><td class="hs">', '')
                actor = string.replace(actor, ' class="even"><td class="hs">', '')
##                print actor
                actor = string.replace(actor, '<a href="http://resume.imdb.com/" onClick="(new Image()).src=\'/rg/title-tease/resumehead/images/b.gif?link=http://resume.imdb.com/\';"><img src="http://i.imdb.com/images/tn15/addtiny.gif" width="25" height="31" border="0"></td><td class="nm"><a href="', '')
                actor = string.replace(actor, '<a href="/rg/title-tease/resumehead/http://resume.imdb.com/"><img src="http://i.imdb.com/images/tn15/addtiny.gif" width="25" height="31" border="0"></td><td class="nm"><a href="', '')
                actor = string.replace(actor, '<a href="/rg/title-tease/resumehead/http://resume.imdb.com/"><img src="http://i.ec.imdb.com/images/tn15/addtiny.gif" width="25" height="31" border="0"></td><td class="nm"><a href="', '')
                pat = '<a href="/rg/title-tease/tinyhead/name/nm\d\d\d\d\d\d\d/"><img src="http://ia.?e?c?.imdb.com/media/imdb/.*?.j?p?g?g?i?f?" width="23" height="32" border="0"></a><br></td><td class="nm"><a href="'
                tiny = re.findall(re.compile(pat), actor)
##                print actor
                if tiny:
                    actor = string.replace(actor, tiny[0], '')
##                print actor
                pat = '<a href="/name/nm\d\d\d\d\d\d\d/" onClick="\(new Image\(\)\).src=\'/rg/title-tease/tinyhead/images/b.gif\?link=/name/nm\d\d\d\d\d\d\d/\';"><img src="http://ia.imdb.com/media/imdb/[\w\W]*.jpg" width="23" height="32" border="0"></a><br></td><td class="nm"><a href="'
                tiny = re.findall(re.compile(pat), actor)
                if tiny:
                    actor = string.replace(actor, tiny[0], '')
                actor = string.replace(actor, ' ... ', '')
                actor = string.split(actor, '</a></td><td class="ddd"></td><td class="char">')
                actor[0] = string.split(actor[0], '">')
                actor[0][0] = "http://www.imdb.com" + actor[0][0]
                actor[1] = string.replace(actor[1], '<a href="quotes">', '')
                actor[1] = string.replace(actor[1], '</a>', '')
                actor[1] = string.replace(actor[1], '</td></tr>', '')
                pat = '<a href="/character/ch\d\d\d\d\d\d\d/">'
                tiny = re.findall(re.compile(pat), actor[1])
                if tiny:
                    for tin in tiny:
                        actor[1] = string.replace(actor[1], tin, '')
##                print actor
                SQL = "SELECT id FROM movies.person WHERE adress = '" + pg.escape_string(actor[0][0]) + "'"
                SQL = SQL.decode('iso8859-1').encode('utf-8')
                res = db.query(SQL).dictresult()
                try:
                    actorID = res[0]['id']
                except:
                    SQL = "INSERT INTO movies.person (adress, name) VALUES ('" + pg.escape_string(actor[0][0]) + "', '" + pg.escape_string(actor[0][1]) + "')"
                    SQL = SQL.decode('iso8859-1').encode('utf-8')
                    db.query(SQL)
                    SQL = "SELECT currval('movies.person_id_seq')"
                    actorID = db.query(SQL).dictresult()[0]['currval']
                if actor[1] == '':
                    SQL = "SELECT nextval('movies.nothing')"
                    actor[1] = "nothing" + str(db.query(SQL).dictresult()[0]['nextval'])
                SQL = "SELECT person FROM movies.person_movie WHERE person = " + str(actorID) + " AND movie = " + str(movie['id']) + " AND description = '" + pg.escape_string(actor[1]) + "' AND \"type\" = 3"
                SQL = SQL.decode('iso8859-1').encode('utf-8')
                res = db.query(SQL).dictresult()
                try:
                    res[0]['person']
                except:
                    SQL = "INSERT INTO movies.person_movie (\"type\", person, movie, description, \"order\") VALUES (3, " + str(actorID) + ", " + str(movie['id']) + ", '" + pg.escape_string(actor[1]) + "', " + str(i) + ")"
                    SQL = SQL.decode('iso8859-1').encode('utf-8')
                    db.query(SQL)
            if len(cast) > 1:
                cast[1] = string.split(cast[1], '</table><div><form action="/character/create"')[0]
                cast[1] = string.split(cast[1][1:], '</td></tr>');
                for actor in cast[1]:
                    if actor != '':
                        actor = string.replace(actor, '<tr class="odd">', '')
                        actor = string.replace(actor, 'tr class="odd">', '')
                        actor = string.replace(actor, '<tr class="even">', '')
                        actor = string.replace(actor, '<a href="http://resume.imdb.com/" onClick="(new Image()).src=\'/rg/title-tease/resumehead/images/b.gif?link=http://resume.imdb.com/\';"><img src="http://i.imdb.com/images/tn15/addtiny.gif" width="25" height="31" border="0"></td><td class="nm"><a href="', '')
                        actor = string.replace(actor, '<td class="hs"><a href="/rg/title-tease/resumehead/http://resume.imdb.com/"><img src="http://i.imdb.com/images/tn15/addtiny.gif" width="25" height="31" border="0"></td><td class="nm">', '')
                        actor = string.replace(actor, '<td class="hs"><a href="/rg/title-tease/resumehead/http://resume.imdb.com/"><img src="http://i.ec.imdb.com/images/tn15/addtiny.gif" width="25" height="31" border="0"></td><td class="nm"><a href="', '')
                        pat = '<a href="/rg/title-tease/tinyhead/name/nm\d\d\d\d\d\d\d/"><img src="http://ia.?e?c?.imdb.com/media/imdb/.*?.j?p?g?g?i?f?" width="23" height="32" border="0"></a><br></td><td class="nm"><a href="'
                        tiny = re.findall(re.compile(pat), actor)
    ##                    print actor
                        if tiny:
                            actor = string.replace(actor, tiny[0], '')
                        pat = '<a href="/name/nm\d\d\d\d\d\d\d/" onClick="\(new Image\(\)\).src=\'/rg/title-tease/tinyhead/images/b.gif\?link=/name/nm\d\d\d\d\d\d\d/\';"><img src="http://ia.imdb.com/media/imdb/[\w\W]*.jpg" width="23" height="32" border="0"></a><br></td><td class="nm"><a href="'
                        tiny = re.findall(re.compile(pat), actor)
                        if tiny:
                            actor = string.replace(actor, tiny[0], '')
                        actor = string.replace(actor, ' ... ', '')
                        actor = string.split(actor, '</a></td><td class="ddd"></td><td class="char">')
                        actor[0] = string.replace(actor[0], '<a href="', '')
                        actor[0] = string.replace(actor[0], '<td class="hs">', '')
                        actor[0] = string.split(actor[0], '">')
                        actor[0][0] = "http://www.imdb.com" + actor[0][0]
##                        print actor
                        actor[1] = string.replace(actor[1], '<a href=quotes">', '')
                        actor[1] = string.replace(actor[1], '<a href="quotes">', '')
                        actor[1] = string.replace(actor[1], '</a>', '')
                        pat = '<a href="/character/ch\d\d\d\d\d\d\d/">'
                        tiny = re.findall(re.compile(pat), actor[1])
                        if tiny:
                            for tin in tiny:
                                actor[1] = string.replace(actor[1], tin, '')
##                        print actor
                        SQL = "SELECT id FROM movies.person WHERE adress = '" + pg.escape_string(actor[0][0]) + "'"
                        SQL = SQL.decode('iso8859-1').encode('utf-8')
                        res = db.query(SQL).dictresult()
                        try:
                            actorID = res[0]['id']
                        except:
                            SQL = "INSERT INTO movies.person (adress, name) VALUES ('" + pg.escape_string(actor[0][0]) + "', '" + pg.escape_string(actor[0][1]) + "')"
                            SQL = SQL.decode('iso8859-1').encode('utf-8')
                            db.query(SQL)
                            SQL = "SELECT currval('movies.person_id_seq')"
                            actorID = db.query(SQL).dictresult()[0]['currval']
                        if actor[1] == '':
                            SQL = "SELECT nextval('movies.nothing')"
                            actor[1] = "nothing" + str(db.query(SQL).dictresult()[0]['nextval'])
                        SQL = "SELECT person FROM movies.person_movie WHERE person = " + str(actorID) + " AND movie = " + str(movie['id']) + " AND description = '" + pg.escape_string(actor[1]) + "' AND \"type\" = 3"
                        SQL = SQL.decode('iso8859-1').encode('utf-8')
                        res = db.query(SQL).dictresult()
                        try:
                            res[0]['person']
                        except:
                            SQL = "INSERT INTO movies.person_movie (\"type\", person, movie, description) VALUES (3, " + str(actorID) + ", " + str(movie['id']) + ", '" + pg.escape_string(actor[1]) + "')"
                            SQL = SQL.decode('iso8859-1').encode('utf-8')
                            db.query(SQL)
                        
        #hämtar producers
        producers = re.findall(patProducer, creditsPage)
        if producers:
            producers = string.replace(producers[0][0], '<a class="glossary" name="producers" href="/Glossary/P#producer">Produced by</a></h5></td></tr>', '')
            producers = string.replace(producers, '<tr><td colspan="4">&nbsp;</td></tr></table><table border="0" cellpadding="1" cellspacing="1"><tr><td colspan="3" align="left"><h5><a class="glossary"', '')
            producers = string.split(producers, '</td></tr>')[:-1]
            for producer in producers:
                producer = string.replace(producer, '<tr> <td valign="top"><a href="', '')
                producer = string.replace(producer, ' .... ', '')
                producer = string.split(producer, '</a></td><td valign="top" nowrap="1"></td><td valign="top">')
                if producer[0] != '<tr><td colspan="4">&nbsp;':
                    producer[1] = string.split(producer[1], '">')
                    try:
                        producer[1] = producer[1][1]
                    except:
                        producer[1] = producer[1][0]
                    producer[0] = string.split(producer[0], '">')
                    producer[0][0] = "http://www.imdb.com" + producer[0][0]
                    producer[1] = string.replace(producer[1], '</a> ', '')
##                    print producer
                    SQL = "SELECT id FROM movies.person WHERE adress = '" + pg.escape_string(producer[0][0]) + "'"
                    SQL = SQL.decode('iso8859-1').encode('utf-8')
                    res = db.query(SQL).dictresult()
                    try:
                        producerID = res[0]['id']
                    except:
                        SQL = "INSERT INTO movies.person (adress, name) VALUES ('" + pg.escape_string(producer[0][0]) + "', '" + pg.escape_string(producer[0][1]) + "')"
                        SQL = SQL.decode('iso8859-1').encode('utf-8')
                        db.query(SQL)
                        SQL = "SELECT currval('movies.person_id_seq')"
                        producerID = db.query(SQL).dictresult()[0]['currval']
                    if producer[1] == '':
                        SQL = "SELECT nextval('movies.nothing')"
                        producer[1] = "nothing" + str(db.query(SQL).dictresult()[0]['nextval'])
                    SQL = "SELECT person FROM movies.person_movie WHERE person = " + str(producerID) + " AND movie = " + str(movie['id']) + " AND description = '" + pg.escape_string(producer[1]) + "' AND \"type\" = 4"
                    SQL = SQL.decode('iso8859-1').encode('utf-8')
                    res = db.query(SQL).dictresult()
                    try:
                        res[0]['person']
                    except:
                        SQL = "INSERT INTO movies.person_movie (\"type\", person, movie, description) VALUES (4, " + str(producerID) + ", " + str(movie['id']) + ", '" + pg.escape_string(producer[1]) + "')"
                        SQL = SQL.decode('iso8859-1').encode('utf-8')
                        db.query(SQL)
        
        #lägger till den nya infon som hittats
        SQL = "UPDATE movies.movie SET grade = '" + pg.escape_string(grade) + "', \"year\" = " + pg.escape_string(year) + ", \"numberOfVotes\" = " + str(votes) + ", edited = now() WHERE imdb = '" + movie['imdb'] + "'"
        db.query(SQL)
        
    
getMovieInfo()