IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__gcalendar]') AND type in (N'U'))
BEGIN
	CREATE TABLE [#_gcalendar](
		[id] [bigint] IDENTITY(1,1) NOT NULL,
		[calendar_id] [text] NOT NULL,
		[name] [text] NOT NULL,
		[magic_cookie] [text] NOT NULL,
		[username] [varchar](255) NULL,
		[password] [text] NULL,
		[color] [text] NOT NULL,
		[access] [int] NOT NULL,
		[access_content] [int] NOT NULL
	),
 CONSTRAINT [PK_#__gcalendar_id] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;