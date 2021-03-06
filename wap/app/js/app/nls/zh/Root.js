define({
	/**
	 * 新加国际化字段时,为保证页面文案的独立性,按照页面定义key,且该页面里的所有国际化字段的key都有页面前缀.
	 * (本规则除公共的以外)
	 * 例如:标签分类的key   "tagCate+自定义":"value"
	 */
	//public
	"status": "状态",
	"calcStatus": "计算状态",
	"search": "搜索",
	"creator": "创建人",
	"createTime": "创建时间",
	"reset": "重置",
	"operation": "操作",
	"edit": "编辑",
	"toDelete": "删除",
	"log": "日志",
	"moveUp": "上移",
	"moveDown": "下移",
	"lookUp": "查看",
	"pleaseSelect": "请选择",
	"pleaseInput": "请输入",
	"save": "保存",
	"noSave":"不保存",
	"cancel": "取消",
	"buildJson": "生成json",
	"recalculate": "重新计算",
	"describe":"描述",
	"noData":"暂无数据",
	"remove":"移除",
	"rename":"重命名",
	"confirmDelete":"确定要删除",
	
	
	//标签定义--html
	"addBasicTag":"添加基础标签",
	"editBasicTag":"编辑基础标签",
	"labelDefinition": "添加基础标签",
	"tagName": "标签名称",
	"touchPointTypes": "人群类型",
	"tagSources": "标签来源",
	"tagType": "标签类型",
	"changeTagDataTime": "人群更新时间",
	"allTags": "全部标签",
	"backTagList": "返回基础标签列表",
	"tagNameMaxLength": "标签名称最多支持256个字符",
	"tagCategory": "标签分类",
	"uiData": "UI数据",
	"serverData": "服务器数据",
	"searchCritera": "搜索条件",
	"inputKeyword": "请输入关键字",
	"populationAttributes":"人群属性",
	//js
	"noPowerDelOtherTags": "您没有权限删除他人创建的标签",
	"inputTypeName": "输入分类名称",
	"labelDefinitionList": "基础标签定义列表",
	"confirmDelTags": "确定要删除标签",
	"addTagSuccess": "标签添加成功",
	"editTagSuccess": "标签编辑成功",
	"inputTagName": "请输入标签名称",
	"createCrowdAttrOrHavior": "请创建人群属性或者人群行为条件",
	"onlyZmSzXhx": "只能是字母，数字和下划线",
	"tagRecalculateSuccess": "标签发起重新计算成功",
	"tagRecalculateConfirms": "标签已被修改，是否需要在重新计算前保存修改？",
	
	//业务标签--html
	"businessTagList":"业务标签列表",
	"businessTagName":"业务标签名称",
	"backBusinessTagList":"返回业务标签列表",
	"viewBusinessTag":"查看业务标签",
	"addBusinessTag":"添加业务标签",
	"editBusinessTag":"编辑业务标签",
	"businessTagNameRequire":"业务标签名称最多支持256个字符",
	"createBusinessTag": "请创建业务标签的条件",
	
	//系统标签--html
	"systemTagList":"系统标签列表",
	"addSystemTag":"添加系统标签",
	"systemTagName":"系统标签名称",
	"backSystemTagList":"返回系统标签列表",
	"viewSystemTag":"查看系统标签",
	"addSystemTag":"添加系统标签",
	"editSystemTag":"编辑系统标签",
	"systemTagNameRequire":"系统标签名称最多支持256个字符",
	"selectTouchPointTypes":"请选择人群类型",
	"calculationScript":"计算脚本",
	"selectCalculationScript":"请选择计算脚本",
	"operatingParameters":"运行参数",
	"scriptParamRequire":'注意：脚本必须放在TD Batch Manager的Executor所在机器上，并且Executor有权限访问到该脚本文件（包括该脚本会访问的文件目录和文件）脚本参数采用：-a #${paramName1}# -b #{paramName2}#,参数中不要出现""。',
	"inputOperatingParameters":"请输入运行参数",
	"describeRequire":"描述最多支持512个字符",
	//js
	"confirmDelSysTags":"确定要删除系统标签",
	"noRecalculate":"任务正在执行，不能重新计算",
	"systemTagAddSuccess":"系统标签添加成功",
	"systemTagEditSuccess":"系统标签编辑成功",
	"inputSystemTagName":"请输入系统标签名称",
	"countingAgainLater":"正在重新计算，请稍后再试",
	"systemTagCalcuSuccess":"系统标签发起重新计算成功",
	
	//标签分类--html
	"tagCateRmTag":"系统标签发起重新计算成功",
	"tagCateCategory":"分类",
	"tagCateTagCategory":"标签分类",
	"tagCateBasicInfo":"基础信息",
	"tagCateCateName":"分类名称",
	"tagCateNumOfSubCates":"子分类数量",
	"tagCateTagNum":"标签数量",
	"tagCateSameTagPct":"同级分类标签占比",
	"tagPctOfSubTag":"子分类标签占比",
	"tagCateTag":"标签",
	"tagCateAddTag":"添加标签",
	"tagCateCurrentCate":"当前分类",
	"tagCateSeledTags":"已选标签",
	"tagCateSelTag":"选择标签",
	"tagCateTagNamePrompt":"请输入标签名称，输入结束后可回车确认",
	"tagCateAddCate":"添加分类",
	"tagCateEditCate":"编辑分类",
	"tagCatePreCate":"上级分类",
	"tagCateSelPreCate":"请选择上级分类",
	"tagCateNameUp12":"分类名称最多支持12个字符",
	"tagCateInCateName":"请输入分类名称",
	"tagCateIsPortrait":"是否可作为人群画像",
	"tagCateIsPortraitDesc":"勾选后，此分类可在人群画像中展示",
	"tagCateIsTag":"是否可作为标签",
	"tagCateIsTagDesc":"勾选后，此分类可作为标签使用",
	"tagCateConfigNextLevel":"由下一级内容汇聚而成",
	"tagCateConfigCalcScript":"由脚本计算生成",
	"tagCateIsAutoTag":"是否使用自动标签",
	"tagCateIsAutoTagDesc":"勾选后，此分类可直接使用属性值作为标签",
	"tagCateAccOrAccAttr":"账户或账户属性",
	"tagCateAttr":"属性",
	"tagCateRunParam":"运行参数",
	
	/*人群洞察配置*/
	"backCrowdPortraitGroupList":"返回画像组列表",
	"addCrowdPortrait":"新增画像",
	"editCrowdPortrait":"编辑画像",
	"crowdPortraitList":"画像列表",
	"addCommonCrowdPortraitGroup":"新增普通画像组",
	"addPositionCrowdPortraitGroup":"新增地理位置画像组",
	"addCrowdPortraitGroup":"新增画像组",
	"editCrowdPortraitGroup":"编辑画像组",
	"crowdPortraitGroupList":"画像组列表",
	"crowdPortraitGroupName":"画像组名称",
	"crowdPortraitGroupType":"画像组类型",
	"crowdPortraitGroupAccount":"所属账户",
	
	//js
	"tagCateUnDel":"不能删除，请先删除",
	"tahCateSubCategory":"的子分类",
	"tagCateSelectUp20":"一次最多只能选择20个标签，请保存后再操作",
	"tagCateNoRepeatAddTag":"此标签已经添加了，不能重复添加",
	"tagCateNoCheckedTag":"此标签未查询到，请重新输入",
	"tagCateSelTagThanSave":"请选择标签后再保存",
	"tagCateRmTagFromCate":"确定要从当前分类下移除标签",
	"tagCateSelTagThanRm":"请选择标签后再移除",
	"tagCateRmTagSuccess":"恭喜您，成功移除标签",
	"tagCateSelRmTag":"请选择需要移除标签的分类",
	"tagCateNewSubCate":"新建子分类",
	"tagCateExistUnDel":"存在子分类或标签，不能删除!",
	"tagCateNodeNameEmpty":"节点名称不能为空.",
	"tagCateUpFailNodeNameRepeat":"编辑失败，同级的节点名称不能重复",
	"tagCateSelTagChooseWay":"勾选作为标签后，必须选择一种方式",
	"tagCateInRunParam":"请输入运行参数",
	
	//TODO:项目整体国际化完成后删除
	"todo": "todo"
});